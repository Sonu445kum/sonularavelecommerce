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
            return $item->product->price * $item->quantity;
        });

        $cart->update(['subtotal' => $subtotal]);

        return view('cart.index', compact('cart', 'subtotal'));
    }

    /**
     * ➕ Add Product to Cart (with stock update)
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
        $requestedQty = $validated['quantity'];

        // ✅ Check stock availability
        if ($product->stock < $requestedQty) {
            return redirect()->back()->with('error', '⚠️ Only ' . $product->stock . ' items left in stock!');
        }

        // ✅ Get or create user's cart
        $cart = Cart::firstOrCreate(['user_id' => $user->id]);

        // ✅ Check if product already in cart
        $cartItem = $cart->items()->where('product_id', $product->id)->first();

        if ($cartItem) {
            $newQty = $cartItem->quantity + $requestedQty;

            // Prevent exceeding stock
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

        // ✅ Decrease stock dynamically
        $product->decrement('stock', $requestedQty);

        // ✅ Update subtotal
        $cart->update(['subtotal' => $cart->calculateSubtotal()]);

        // ✅ Save last selected quantity in session
        Session::put('last_selected_quantity_' . $product->id, $requestedQty);

        return redirect()->back()->with('success', '✅ Product added to cart successfully! Stock updated.');
    }

    /**
     * 🔄 Update Quantity (also adjusts stock)
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

        // ✅ If increasing quantity
        if ($difference > 0) {
            if ($product->stock < $difference) {
                return redirect()->back()->with('error', '⚠️ Only ' . $product->stock . ' more items left!');
            }
            $product->decrement('stock', $difference);
        }
        // ✅ If decreasing quantity
        elseif ($difference < 0) {
            $product->increment('stock', abs($difference));
        }

        $cartItem->update(['quantity' => $newQty]);

        // ✅ Update subtotal
        $cart = $cartItem->cart;
        $cart->update(['subtotal' => $cart->calculateSubtotal()]);

        // ✅ Update session quantity
        Session::put('last_selected_quantity_' . $cartItem->product_id, $newQty);

        return redirect()->back()->with('success', '🛍️ Cart updated successfully! Stock adjusted.');
    }

    /**
     * ❌ Remove Item from Cart (restore stock)
     */
    public function remove($id)
    {
        $cartItem = CartItem::findOrFail($id);
        $cart = $cartItem->cart;
        $product = $cartItem->product;

        // ✅ Restore stock
        $product->increment('stock', $cartItem->quantity);

        // Remove session
        Session::forget('last_selected_quantity_' . $cartItem->product_id);

        $cartItem->delete();

        // ✅ Update subtotal
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
                // Restore stock for each product
                $item->product->increment('stock', $item->quantity);
                Session::forget('last_selected_quantity_' . $item->product_id);
            }

            $cart->items()->delete();
            $cart->update(['subtotal' => 0]);
        }

        return redirect()->back()->with('success', '🧹 Cart cleared and stock restored!');
    }
}
