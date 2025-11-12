<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use Illuminate\Support\Facades\Session;
use App\Http\Controllers\CouponController;

class CartController extends Controller
{
    // ================= Show Cart =================
    public function index()
    {
        $cart = Cart::with('items.product')->where('user_id', Auth::id())->first();

        if (!$cart || $cart->items->isEmpty()) {
            return view('cart.index', ['cart' => null, 'message' => 'Your cart is empty!']);
        }

        $subtotal = $cart->items->sum(fn($item) => $item->price * $item->quantity);
        $cart->update(['subtotal' => $subtotal]);
        $coupon = Session::get('coupon', null);

        return view('cart.index', compact('cart', 'subtotal', 'coupon'));
    }

    // ================= Add Product to Cart =================
    public function add(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity'   => 'required|integer|min:1',
        ]);

        $product = Product::findOrFail($validated['product_id']);
        $qty = $validated['quantity'];

        if ($product->stock < $qty) {
            return redirect()->back()->with('error', '⚠️ Only ' . $product->stock . ' items left in stock!');
        }

        $cart = Cart::firstOrCreate(['user_id' => Auth::id()]);
        $cartItem = $cart->items()->where('product_id', $product->id)->first();

        if ($cartItem) {
            $newQty = $cartItem->quantity + $qty;
            if ($newQty > $product->stock) {
                return redirect()->back()->with('error', '⚠️ Not enough stock available!');
            }
            $cartItem->update(['quantity' => $newQty, 'price' => $product->price]);
        } else {
            $cart->items()->create([
                'product_id' => $product->id,
                'quantity'   => $qty,
                'price'      => $product->price
            ]);
        }

        $product->decrement('stock', $qty);
        $cart->update(['subtotal' => $cart->items->sum(fn($i) => $i->price * $i->quantity)]);
        Session::put('last_selected_quantity_' . $product->id, $qty);

        return redirect()->back()->with('success', '✅ Product added to cart successfully!');
    }

    // ================= Update Quantity (AJAX) =================
    public function update(Request $request, $itemId)
    {
        try {
            $request->validate([
                'quantity' => 'required|integer|min:1'
            ]);

            $cart = Cart::where('user_id', auth()->id())->firstOrFail();
            $item = $cart->items()->where('id', $itemId)->firstOrFail();

            // Update quantity
            $item->quantity = $request->quantity;
            $item->save();

            // Recalculate subtotal & total
            $cartSubtotal = $cart->items->sum(fn($i) => $i->price * $i->quantity);

            // Get discount from CouponController
            $discount = CouponController::calculateDiscount($cartSubtotal);

            $shipping = 50;
            $total = max(($cartSubtotal - $discount) + $shipping, 0);

            return response()->json([
                'success' => true,
                'itemSubtotal' => round($item->price * $item->quantity, 2),
                'cartSubtotal' => round($cartSubtotal, 2),
                'discount' => round($discount, 2),
                'total' => round($total, 2)
            ]);

        } catch (\Exception $e) {
            \Log::error("Cart Update Error: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Server error: ' . $e->getMessage()
            ], 500);
        }
    }

    // ================= Remove Item =================
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


    public function clear()
    {
        try {
            $cartItems = CartItem::where('user_id', Auth::id())->get();
            foreach ($cartItems as $item) {
                $item->product->increment('stock', $item->quantity);
                Session::forget('last_selected_quantity_' . $item->product_id);
            }
            CartItem::where('user_id', Auth::id())->delete();

            return response()->json([
                'success' => true,
                'message' => '🧹 Cart cleared and stock restored!',
                'cartSubtotal' => 0,
                'discount' => 0,
                'shipping' => 50,
                'total' => 50
            ]);
        } catch (\Exception $e) {
            \Log::error("Clear Cart Error: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Server error: ' . $e->getMessage()
            ], 500);
        }
    }
}
