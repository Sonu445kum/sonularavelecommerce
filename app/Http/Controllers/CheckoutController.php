<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
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
        $user = Auth::user();
        $cart = null;
        $cartItems = collect();

        if ($user) {
            $cart = Cart::with('items.product.images')->where('user_id', $user->id)->first();
            if ($cart && $cart->items->isNotEmpty()) {
                $cartItems = $cart->items;
            }
        } else {
            $sessionCart = session()->get('cart', []);
            if (!empty($sessionCart)) {
                $productIds = collect($sessionCart)->pluck('product_id')->toArray();
                $products = Product::with('images')->whereIn('id', $productIds)->get()->keyBy('id');

                foreach ($sessionCart as $item) {
                    $product = $products->get($item['product_id']);
                    if (!$product) continue;

                    $qty = $item['quantity'] ?? 1;
                    $price = $item['price'] ?? $product->price;

                    $cartItems->push((object) [
                        'product' => $product,
                        'quantity' => $qty,
                        'price' => $price,
                        'total' => $price * $qty,
                    ]);
                }
            }
        }

        if ($cartItems->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Your cart is empty!');
        }

        $subtotal = $cartItems->sum(fn($item) => $item->price * $item->quantity);

        $coupon = session('coupon', null);
        if ($coupon) {
            $coupon = [
                'code' => $coupon['code'] ?? null,
                'discount_type' => $coupon['type'] ?? 'fixed',
                'discount_value' => $coupon['value'] ?? 0,
            ];
        }

        return view('checkout.index', compact('cart', 'cartItems', 'subtotal', 'coupon'));
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
            })->first();

        if (!$coupon) {
            return back()->with('error', 'Your coupon is not valid.');
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

        // 🛒 Get Cart Items
        $cartItems = collect();
        $subtotal = 0;

        if ($isGuest) {
            $sessionCart = session()->get('cart', []);
            if (empty($sessionCart)) {
                return redirect()->route('cart.index')->withErrors('Your cart is empty.');
            }

            $productIds = collect($sessionCart)->pluck('product_id')->toArray();
            $products = Product::with('images')->whereIn('id', $productIds)->get()->keyBy('id');

            foreach ($sessionCart as $item) {
                $product = $products->get($item['product_id']);
                if (!$product) continue;

                $qty = $item['quantity'] ?? 1;
                $price = $item['price'] ?? $product->price;

                $cartItems->push((object) [
                    'product' => $product,
                    'quantity' => $qty,
                    'price' => $price,
                    'total' => $price * $qty,
                ]);

                $subtotal += $price * $qty;
            }
        } else {
            $cart = Cart::with('items.product.images')->where('user_id', $user->id)->first();
            if (!$cart || $cart->items->isEmpty()) {
                return redirect()->route('cart.index')->withErrors('Your cart is empty.');
            }
            $cartItems = $cart->items;
            $subtotal = $cartItems->sum(fn($item) => $item->price * $item->quantity);
        }

        // 💰 Coupon
        $coupon = session('coupon', []);
        $discount = 0;
        $couponCode = null;
        if (!empty($coupon)) {
            $couponCode = $coupon['code'] ?? null;
            $discount = ($coupon['type'] ?? 'fixed') === 'percent'
                ? $subtotal * (($coupon['value'] ?? 0)/100)
                : ($coupon['value'] ?? 0);
        }

        $shipping = 50;
        $totalAmount = max(0, $subtotal - $discount + $shipping);

        // ✅ Save Shipping Address
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

        // DB Transaction
        DB::beginTransaction();
        try {
            // Create Order
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
                'status' => $req->payment_method === 'cod' ? 'Processing' : 'Pending',
                'payment_status' => $req->payment_method === 'cod' ? 'Pending' : 'Paid',
            ]);

            foreach ($cartItems as $item) {
                $unitPrice = $item->price ?? ($item->product->price ?? 0);
                $order->items()->create([
                    'product_id' => $item->product->id,
                    'quantity' => $item->quantity,
                    'product_name' => $item->product->name,
                    'product_image' => $item->product->featured_image ?? ($item->product->images->first()->path ?? 'images/default.jpg'),
                    'unit_price' => $unitPrice,
                    'total_price' => $unitPrice * $item->quantity,
                ]);
            }

            // Notifications
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
                    'title' => $req->payment_method === 'cod' ? 'New COD Order Received' : '🛒 New Order Received',
                    'message' => "New order #{$order->id} from {$customerName} ({$customerEmail}).",
                    'type' => 'admin_order',
                    'is_read' => false,
                ]);
            }

            // Emails
            try {
                Mail::to($customerEmail)->send(new OrderConfirmation($order));
                foreach ($admins as $admin) {
                    Mail::to($admin->email)->send(new AdminNewOrderMail($order, $user ?? null));
                }
            } catch (Exception $e) {
                \Log::error('Email sending failed: ' . $e->getMessage());
            }

            // Stripe Payment
            if (in_array($req->payment_method, ['card', 'razorpay'])) {
                Stripe::setApiKey(config('services.stripe.secret'));
                $paymentIntent = PaymentIntent::create([
                    'amount' => (int) round($totalAmount * 100),
                    'currency' => 'inr',
                    'metadata' => ['order_id' => $order->id, 'user_id' => $user->id ?? null],
                ]);
                $order->update(['payment_intent_id' => $paymentIntent->id]);
            }

            // Clear Cart
            if (!$isGuest) {
                $cart->items()->delete();
                $cart->delete();
            } else {
                session()->forget('cart');
            }
            session()->forget('coupon');

            DB::commit();
            return redirect()->route('checkout.success', ['order_id' => $order->id])
                         ->with('success', 'Order placed successfully!');
        } catch (Exception $e) {
            DB::rollBack();
            \Log::error('Checkout failed: '.$e->getMessage());
            return back()->withErrors('Checkout failed: '.$e->getMessage());
        }
    }

    /**
     * ✅ Success Page
     */
    public function success(Request $req)
    {
        $order = Order::with(['address','items.product.images'])->find($req->order_id);
        if (!$order) return redirect()->route('home')->withErrors('Order not found.');

        // Mark as Paid
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
     * ❌ Cancel Page
     */
    public function cancel()
    {
        return view('checkout.cancel')->withErrors('Payment cancelled.');
    }
}
