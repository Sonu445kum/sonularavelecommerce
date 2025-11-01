<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;

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

        // ✅ Calculate subtotal (reliable)
        $subtotal = $cart->items->sum(function ($item) {
            return $item->product->price * $item->quantity;
        });

        $cart->update(['subtotal' => $subtotal]);

        return view('cart.index', compact('cart', 'subtotal'));
    }

    /**
     * ➕ Add Product to Cart
     */
    public function add(Request $request)
    {
        // ✅ Validate request
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity'   => 'required|integer|min:1',
        ]);

        $user = Auth::user();
        $product = Product::findOrFail($validated['product_id']);

        // ✅ Get or create user's cart
        $cart = Cart::firstOrCreate(['user_id' => $user->id]);

        // ✅ Check if product already in cart
        $cartItem = $cart->items()->where('product_id', $product->id)->first();

        if ($cartItem) {
            // Update existing item
            $cartItem->update([
                'quantity' => $cartItem->quantity + $validated['quantity'],
                'price' => $product->price,
            ]);
        } else {
            // Create new item
            $cart->items()->create([
                'product_id' => $product->id,
                'quantity'   => $validated['quantity'],
                'price'      => $product->price,
            ]);
        }

        // ✅ Update subtotal
        $cart->update(['subtotal' => $cart->calculateSubtotal()]);

        return redirect()->back()->with('success', '✅ Product added to cart successfully!');
    }

    /**
     * 🔄 Update Quantity
     */
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'quantity' => 'required|integer|min:1',
        ]);

        $cartItem = CartItem::findOrFail($id);
        $cartItem->update(['quantity' => $validated['quantity']]);

        // ✅ Update subtotal
        $cart = $cartItem->cart;
        $cart->update(['subtotal' => $cart->calculateSubtotal()]);

        return redirect()->back()->with('success', '🛍️ Cart updated successfully!');
    }

    /**
     * ❌ Remove Item from Cart
     */
    public function remove($id)
    {
        $cartItem = CartItem::findOrFail($id);
        $cart = $cartItem->cart;

        $cartItem->delete();

        // ✅ Update subtotal
        $cart->update(['subtotal' => $cart->calculateSubtotal()]);

        return redirect()->back()->with('success', '🗑️ Item removed from cart!');
    }

    /**
     * 🧹 Clear Entire Cart
     */
    public function clear()
    {
        $cart = Cart::where('user_id', Auth::id())->first();

        if ($cart) {
            $cart->items()->delete();
            $cart->update(['subtotal' => 0]);
        }

        return redirect()->back()->with('success', '🧹 Cart cleared successfully!');
    }
}
