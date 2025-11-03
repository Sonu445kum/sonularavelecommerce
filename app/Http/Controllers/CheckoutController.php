<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Coupon;
use Stripe\Stripe;
use Stripe\PaymentIntent;

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
        } else {
            $sessionCart = session()->get('cart', []);
            if (empty($sessionCart)) {
                return redirect()->route('cart.index')->withErrors('Your cart is empty.');
            }

            // Get all product IDs from session cart
            $productIds = collect($sessionCart)->pluck('product_id')->toArray();

            // Eager load products with images
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
        }

        $coupon = session('coupon', null);
        return view('checkout.index', compact('cartItems', 'coupon'));
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

        // ✅ Store coupon data in session with correct keys
        session()->put('coupon', [
            'id' => $coupon->id,
            'code' => $coupon->code,
            'type' => $coupon->type ?? 'fixed',  // default fallback
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
            'payment_method' => 'required|string|in:cod,card,razorpay',
        ]);

        $user = Auth::user();
        $isGuest = !$user;

        // 🛒 Fetch cart
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

        // 💰 Coupon & total calculation (safe version)
        $coupon = session('coupon', []);
        $discount = 0;

        if (!empty($coupon) && isset($coupon['type']) && isset($coupon['value'])) {
            $type = $coupon['type'];
            $value = $coupon['value'];

            if ($type === 'percent') {
                $discount = $subtotal * ($value / 100);
            } else {
                $discount = $value;
            }
        }

        $shipping = 50;
        $totalAmount = max(0, $subtotal - $discount + $shipping);

        /**
         * 💳 Online Payments (Stripe / Razorpay)
         */
        if (in_array($req->payment_method, ['card', 'razorpay'])) {
            DB::beginTransaction();
            try {
                $order = Order::create([
                    'user_id' => $user->id ?? null,
                    'total_amount' => $totalAmount,
                    'subtotal' => $subtotal,
                    'discount' => $discount,
                    'shipping' => $shipping,
                    'address' => $req->address,
                    'payment_method' => $req->payment_method,
                    'status' => 'Pending',
                ]);

                foreach ($items as $item) {
                    $product = $item->product;
                    $productImage = $product->featured_image ?? ($product->images->first()->path ?? 'images/default-product.jpg');

                    $order->items()->create([
                        'product_id'    => $product->id ?? $item->product_id,
                        'quantity'      => $item->quantity,
                        'product_name'  => $product->title ?? $product->name ?? 'Unnamed Product',
                        'product_sku'   => $product->sku ?? 'N/A',
                        'product_image' => $productImage,
                        'unit_price'    => $product->price ?? 0,
                        'total_price'   => ($product->price ?? 0) * $item->quantity,
                    ]);
                }

                // ✅ Stripe Configuration
                $stripeSecret = config('services.stripe.secret') ?? env('STRIPE_SECRET');
                if (empty($stripeSecret)) {
                    DB::rollBack();
                    return redirect()->route('checkout.index')->withErrors('Stripe not configured in .env file.');
                }

                Stripe::setApiKey($stripeSecret);

                $paymentIntent = PaymentIntent::create([
                    'amount' => (int) round($totalAmount * 100),
                    'currency' => 'inr',
                    'metadata' => [
                        'order_id' => $order->id,
                        'user_id' => $user->id ?? 'guest',
                    ],
                ]);

                $order->update(['payment_intent_id' => $paymentIntent->id]);
                DB::commit();

                return redirect()->route('checkout.success', [
                    'order_id' => $order->id,
                    'payment_intent_id' => $paymentIntent->id,
                ]);
            } catch (\Throwable $e) {
                DB::rollBack();
                return back()->withErrors('Payment error: ' . $e->getMessage());
            }
        }

        /**
         * 💵 Cash on Delivery
         */
        DB::beginTransaction();
        try {
            $order = Order::create([
                'user_id' => $user->id ?? null,
                'total_amount' => $totalAmount,
                'subtotal' => $subtotal,
                'discount' => $discount,
                'shipping' => $shipping,
                'address' => $req->address,
                'payment_method' => 'cod',
                'status' => 'Processing',
                'payment_status' => 'pending',
            ]);

            foreach ($items as $item) {
                $product = $item->product;
                $productImage = $product->featured_image ?? ($product->images->first()->path ?? 'images/default-product.jpg');

                $order->items()->create([
                    'product_id'    => $product->id ?? $item->product_id,
                    'quantity'      => $item->quantity,
                    'product_name'  => $product->title ?? $product->name ?? 'Unnamed Product',
                    'product_sku'   => $product->sku ?? 'N/A',
                    'product_image' => $productImage,
                    'unit_price'    => $product->price ?? 0,
                    'total_price'   => ($product->price ?? 0) * $item->quantity,
                ]);
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
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->withErrors('Unable to place order: ' . $e->getMessage());
        }
    }

    /**
     * ✅ Payment success page
     */
    public function success(Request $request)
    {
        $order = Order::find($request->order_id);

        if (!$order) {
            return redirect()->route('home')->withErrors('Order not found.');
        }

        $order->update([
            'status' => 'Processing',
            'payment_status' => 'Paid',
            'paid_at' => now(),
        ]);

        if ($order->user_id) {
            $cart = Cart::where('user_id', $order->user_id)->first();
            if ($cart) {
                $cart->items()->delete();
                $cart->delete();
            }
        }

        return view('checkout.success', compact('order'));
    }

    /**
     * ❌ Payment cancel page
     */
    public function cancel()
    {
        return view('checkout.cancel')->withErrors('Payment cancelled. Please try again.');
    }
}
