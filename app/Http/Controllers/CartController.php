<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use Illuminate\Support\Facades\Session;

class CartController extends Controller
{
    /**
     * 🛒 Show logged-in user's cart
     */
    public function index()
    {
        $user = Auth::user();

        $cart = Cart::with('items.product')->where('user_id', $user->id)->first();

        if (!$cart || $cart->items->isEmpty()) {
            return view('cart.index', [
                'cart' => null,
                'message' => 'Your cart is empty!',
            ]);
        }

        // ✅ Calculate subtotal
        $subtotal = $cart->items->sum(function ($item) {
            return $item->price * $item->quantity;
        });

        $cart->update(['subtotal' => $subtotal]);

        // ✅ Pass coupon from session
        $coupon = session('coupon', null);

        return view('cart.index', compact('cart', 'subtotal', 'coupon'));
    }

    /**
     * ➕ Add Product to Cart (with stock update)
     */
    public function add(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity'   => 'required|integer|min:1',
        ]);

        $user = Auth::user();
        $product = Product::findOrFail($validated['product_id']);
        $requestedQty = $validated['quantity'];

        if ($product->stock < $requestedQty) {
            return redirect()->back()->with('error', '⚠️ Only ' . $product->stock . ' items left in stock!');
        }

        $cart = Cart::firstOrCreate(['user_id' => $user->id]);

        $cartItem = $cart->items()->where('product_id', $product->id)->first();

        if ($cartItem) {
            $newQty = $cartItem->quantity + $requestedQty;
            if ($newQty > $product->stock) {
                return redirect()->back()->with('error', '⚠️ Not enough stock available!');
            }
            $cartItem->update([
                'quantity' => $newQty,
                'price' => $product->price,
            ]);
        } else {
            $cart->items()->create([
                'product_id' => $product->id,
                'quantity'   => $requestedQty,
                'price'      => $product->price,
            ]);
        }

        $product->decrement('stock', $requestedQty);
        $cart->update(['subtotal' => $cart->calculateSubtotal()]);
        Session::put('last_selected_quantity_' . $product->id, $requestedQty);

        return redirect()->back()->with('success', '✅ Product added to cart successfully! Stock updated.');
    }

    /**
     * 🔄 Update Quantity (AJAX Friendly)
     */
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'quantity' => 'required|integer|min:1',
        ]);

        $cartItem = CartItem::findOrFail($id);
        $product = $cartItem->product;

        $oldQty = $cartItem->quantity;
        $newQty = $validated['quantity'];
        $difference = $newQty - $oldQty;

        // ✅ Adjust stock
        if ($difference > 0) {
            if ($product->stock < $difference) {
                return response()->json([
                    'status' => false,
                    'message' => '⚠️ Only ' . $product->stock . ' more items left!',
                ]);
            }
            $product->decrement('stock', $difference);
        } elseif ($difference < 0) {
            $product->increment('stock', abs($difference));
        }

        $cartItem->update(['quantity' => $newQty]);

        $cart = $cartItem->cart;
        $cart->update(['subtotal' => $cart->calculateSubtotal()]);

        Session::put('last_selected_quantity_' . $cartItem->product_id, $newQty);

        return response()->json([
            'status' => true,
            'item_total' => $cartItem->price * $newQty,
            'cart_subtotal' => $cart->subtotal,
        ]);
    }

    /**
     * ❌ Remove Item from Cart (restore stock)
     */
    public function remove($id)
    {
        $cartItem = CartItem::findOrFail($id);
        $cart = $cartItem->cart;
        $product = $cartItem->product;

        $product->increment('stock', $cartItem->quantity);
        Session::forget('last_selected_quantity_' . $cartItem->product_id);
        $cartItem->delete();

        $cart->update(['subtotal' => $cart->calculateSubtotal()]);

        return redirect()->back()->with('success', '🗑️ Item removed and stock restored!');
    }

    /**
     * 🧹 Clear Entire Cart (restore all stock)
     */
    public function clear()
    {
        $cart = Cart::where('user_id', Auth::id())->first();

        if ($cart) {
            foreach ($cart->items as $item) {
                $item->product->increment('stock', $item->quantity);
                Session::forget('last_selected_quantity_' . $item->product_id);
            }

            $cart->items()->delete();
            $cart->update(['subtotal' => 0]);
        }

        return redirect()->back()->with('success', '🧹 Cart cleared and stock restored!');
    }

    /**
     * 💰 Apply Coupon Code
     */
    public function applyCoupon(Request $request)
    {
        $request->validate([
            'coupon_code' => 'required|string',
        ]);

        $code = $request->coupon_code;

        $validCoupons = [
            'SAVE50' => ['discount_type' => 'fixed', 'discount_value' => 50],
            'OFF10'  => ['discount_type' => 'percent', 'discount_value' => 10],
        ];

        if (!isset($validCoupons[$code])) {
            return redirect()->back()->with('coupon_error', '❌ Invalid coupon code!');
        }

        $coupon = $validCoupons[$code];
        $coupon['code'] = $code;

        Session::put('coupon', $coupon);

        return redirect()->back()->with('coupon_success', '✅ Coupon applied successfully!');
    }

    /**
     * ❌ Remove Coupon
     */
    public function removeCoupon()
    {
        Session::forget('coupon');
        return redirect()->back()->with('success', '✅ Coupon removed successfully!');
    }
}
