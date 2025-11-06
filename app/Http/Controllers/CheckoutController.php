<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Mail\OrderConfirmation;
use App\Mail\AdminNewOrderMail;
use App\Models\Cart;
use App\Models\Order;
use App\Models\Product;
use App\Models\Coupon;
use App\Models\Notification;
use App\Models\User;
use App\Models\Address;
use App\Models\Payment;
use Stripe\Stripe;
use Stripe\PaymentIntent;
use Exception;

class CheckoutController extends Controller
{
    /**
     * 🏠 Show checkout page
     */
    public function index()
    {
        return $this->show();
    }

    /**
     * 📦 Display checkout details
     */
    public function show()
    {
        $user = Auth::user();

        if ($user) {
            $cart = Cart::where('user_id', $user->id)
                ->with(['items.product.images'])
                ->first();

            if (!$cart || $cart->items->isEmpty()) {
                return redirect()->route('cart.index')->withErrors('Your cart is empty.');
            }

            $cartItems = $cart->items;
            $addresses = Address::where('user_id', $user->id)->get();
        } else {
            $sessionCart = session()->get('cart', []);
            if (empty($sessionCart)) {
                return redirect()->route('cart.index')->withErrors('Your cart is empty.');
            }

            $productIds = collect($sessionCart)->pluck('product_id')->toArray();
            $products = Product::with('images')->whereIn('id', $productIds)->get()->keyBy('id');

            $items = [];
            foreach ($sessionCart as $item) {
                $product = $products->get($item['product_id']);
                if (!$product) continue;

                $qty = $item['quantity'] ?? 1;
                $price = $item['price'] ?? $product->price;

                $items[] = (object) [
                    'product' => $product,
                    'quantity' => $qty,
                    'price' => $price,
                    'total' => $price * $qty,
                ];
            }

            $cartItems = collect($items);
            $addresses = collect();
        }

        $coupon = session('coupon', null);
        return view('checkout.index', compact('cartItems', 'coupon', 'addresses'));
    }

    /**
     * 🎟️ Apply coupon
     */
    public function applyCoupon(Request $req)
    {
        $req->validate(['code' => 'required|string']);

        $coupon = Coupon::where('code', $req->code)
            ->where('is_active', true)
            ->where(function ($q) {
                $q->whereNull('expires_at')->orWhere('expires_at', '>', now());
            })
            ->first();

        if (!$coupon) {
            return back()->withErrors('Invalid or expired coupon.');
        }

        session()->put('coupon', [
            'id' => $coupon->id,
            'code' => $coupon->code,
            'type' => $coupon->type ?? 'fixed',
            'value' => $coupon->value ?? 0,
        ]);

        return back()->with('success', 'Coupon applied successfully!');
    }

    /**
     * 🧾 Process checkout order
     */
    public function process(Request $req)
    {
        $req->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email',
            'phone' => 'required|string|max:15',
            'pincode' => 'required|string|max:10',
            'address' => 'required|string|max:1000',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100',
            'payment_method' => 'required|string|in:cod,card,razorpay',
        ]);

        $user = Auth::user();
        $isGuest = !$user;

        $customerName = $user->name ?? $req->input('name');
        $customerEmail = $user->email ?? $req->input('email');

        // 🛒 Get Cart
        if ($isGuest) {
            $sessionCart = session()->get('cart', []);
            if (empty($sessionCart)) {
                return redirect()->route('cart.index')->withErrors('Your cart is empty.');
            }

            $items = collect();
            $subtotal = 0;

            foreach ($sessionCart as $it) {
                $product = Product::find($it['product_id']);
                if (!$product) continue;

                $price = $it['price'] ?? $product->price;
                $qty = $it['quantity'] ?? 1;

                $items->push((object) [
                    'product' => $product,
                    'quantity' => $qty,
                    'price' => $price,
                    'total' => $price * $qty,
                ]);

                $subtotal += $price * $qty;
            }
        } else {
            $cart = Cart::where('user_id', $user->id)
                ->with(['items.product.images'])
                ->first();

            if (!$cart || $cart->items->isEmpty()) {
                return redirect()->route('cart.index')->withErrors('Your cart is empty.');
            }

            $items = $cart->items;
            $subtotal = $items->sum(fn($it) => $it->price * $it->quantity);
        }

        // 💰 Coupon
        $coupon = session('coupon', []);
        $discount = 0;
        $couponCode = null;

        if (!empty($coupon)) {
            $couponCode = $coupon['code'] ?? null;
            $type = $coupon['type'];
            $value = $coupon['value'];

            $discount = $type === 'percent'
                ? $subtotal * ($value / 100)
                : $value;
        }

        $shipping = 50;
        $totalAmount = max(0, $subtotal - $discount + $shipping);

        // ✅ Save structured Shipping Address
        $address_id = null;
        if ($user) {
            $address = Address::create([
                'user_id' => $user->id,
                'label' => 'Shipping Address',
                'name' => $req->name,
                'phone' => $req->phone,
                'address_line1' => $req->address,
                'address_line2' => '',
                'city' => $req->city ?? 'N/A',
                'state' => $req->state ?? 'N/A',
                'postal_code' => $req->pincode,
                'country' => 'India',
                'is_default' => true,
            ]);
            $address_id = $address->id;
        }

        /**
         * 💳 Online Payment
         */
        if (in_array($req->payment_method, ['card', 'razorpay'])) {
            DB::beginTransaction();
            try {
                $order = Order::create([
                    'user_id' => $user->id ?? null,
                    'address_id' => $address_id,
                    'subtotal' => $subtotal,
                    'discount' => $discount,
                    'coupon_code' => $couponCode,
                    'shipping' => $shipping,
                    'total' => $totalAmount,
                    'address' => $req->address,
                    'payment_method' => $req->payment_method,
                    'status' => 'Pending',
                ]);

                foreach ($items as $item) {
                    $product = $item->product;
                    $image = $product->featured_image ?? ($product->images->first()->path ?? 'images/default.jpg');

                    $order->items()->create([
                        'product_id' => $product->id,
                        'quantity' => $item->quantity,
                        'product_name' => $product->title ?? $product->name,
                        'product_image' => $image,
                        'unit_price' => $product->price,
                        'total_price' => $product->price * $item->quantity,
                    ]);
                }

                Notification::create([
                    'user_id' => $user->id ?? null,
                    'title' => 'Order Placed Successfully',
                    'message' => "Your order #{$order->id} has been placed successfully.",
                    'type' => 'order',
                    'is_read' => false,
                ]);

                $admins = User::where('is_admin', true)->get();
                foreach ($admins as $admin) {
                    Notification::create([
                        'user_id' => $admin->id,
                        'title' => '🛒 New Order Received',
                        'message' => "New order #{$order->id} from {$customerName} ({$customerEmail}).",
                        'type' => 'admin_order',
                        'is_read' => false,
                    ]);
                }

                try {
                    Mail::to($customerEmail)->send(new OrderConfirmation($order));
                    foreach ($admins as $admin) {
                        Mail::to($admin->email)->send(new AdminNewOrderMail($order, $user ?? null));
                    }
                } catch (Exception $e) {
                    \Log::error('Email failed: ' . $e->getMessage());
                }

                Stripe::setApiKey(config('services.stripe.secret'));
                $paymentIntent = PaymentIntent::create([
                    'amount' => (int) round($totalAmount * 100),
                    'currency' => 'inr',
                    'metadata' => [
                        'order_id' => $order->id,
                        'user_id' => $user->id ?? null,
                    ],
                ]);

                $order->update(['payment_intent_id' => $paymentIntent->id]);
                DB::commit();

                return redirect()->route('checkout.success', ['order_id' => $order->id]);
            } catch (Exception $e) {
                DB::rollBack();
                \Log::error('Order creation failed: ' . $e->getMessage());
                return back()->withErrors('Payment failed: ' . $e->getMessage());
            }
        }

        /**
         * 💵 COD (Cash on Delivery)
         */
        DB::beginTransaction();
        try {
            $order = Order::create([
                'user_id' => $user->id ?? null,
                'address_id' => $address_id,
                'subtotal' => $subtotal,
                'discount' => $discount,
                'coupon_code' => $couponCode,
                'shipping' => $shipping,
                'total' => $totalAmount,
                'address' => $req->address,
                'payment_method' => 'cod',
                'status' => 'Processing',
                'payment_status' => 'Pending',
            ]);

            foreach ($items as $item) {
                $product = $item->product;
                $image = $product->featured_image ?? ($product->images->first()->path ?? 'images/default.jpg');

                $order->items()->create([
                    'product_id' => $product->id,
                    'quantity' => $item->quantity,
                    'product_name' => $product->title ?? $product->name,
                    'product_image' => $image,
                    'unit_price' => $product->price,
                    'total_price' => $product->price * $item->quantity,
                ]);
            }

            Notification::create([
                'user_id' => $user->id ?? null,
                'title' => 'Order Placed Successfully (COD)',
                'message' => "Your order #{$order->id} placed successfully.",
                'type' => 'order',
                'is_read' => false,
            ]);

            $admins = User::where('is_admin', true)->get();
            foreach ($admins as $admin) {
                Notification::create([
                    'user_id' => $admin->id,
                    'title' => 'New COD Order Received',
                    'message' => "COD Order #{$order->id} from {$customerName} ({$customerEmail}).",
                    'type' => 'admin_order',
                    'is_read' => false,
                ]);
            }

            try {
                Mail::to($customerEmail)->send(new OrderConfirmation($order));
                foreach ($admins as $admin) {
                    Mail::to($admin->email)->send(new AdminNewOrderMail($order, $user ?? null));
                }
            } catch (Exception $e) {
                \Log::error('Email sending failed: ' . $e->getMessage());
            }

            if (!$isGuest) {
                $cart->items()->delete();
                $cart->delete();
            } else {
                session()->forget('cart');
            }

            session()->forget('coupon');
            DB::commit();

            return redirect()->route('checkout.success', ['order_id' => $order->id])
                ->with('success', 'Order placed successfully (COD)!');
        } catch (Exception $e) {
            DB::rollBack();
            \Log::error('COD Order Failed: ' . $e->getMessage());
            return back()->withErrors('Error placing order: ' . $e->getMessage());
        }
    }

    /**
     * ✅ Success Page
     */
    public function success(Request $req)
    {
        $order = Order::with('address')->find($req->order_id);
        if (!$order) {
            return redirect()->route('home')->withErrors('Order not found.');
        }

        $order->update([
            'status' => 'Processing',
            'payment_status' => 'Paid',
            'paid_at' => now(),
        ]);

        Payment::create([
            'order_id' => $order->id,
            'transaction_id' => $order->payment_intent_id ?? ('STRIPE-' . strtoupper(uniqid())),
            'status' => 'success',
            'method' => $order->payment_method ?? 'stripe',
            'amount' => $order->total ?? 0,
            'meta' => json_encode([
                'user_id' => $order->user_id,
                'coupon' => $order->coupon_code,
                'paid_at' => now()->toDateTimeString(),
            ]),
        ]);

        if ($order->user_id) {
            $cart = Cart::where('user_id', $order->user_id)->first();
            if ($cart) {
                $cart->items()->delete();
                $cart->delete();
            }
        }

        $coupon = $order->coupon_code ?? null;

        return view('checkout.success', compact('order', 'coupon'))
            ->with('success', 'Payment recorded successfully!');
    }

    /**
     * ❌ Cancel page
     */
    public function cancel()
    {
        return view('checkout.cancel')->withErrors('Payment cancelled.');
    }
}
