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
    try {
        $item = CartItem::with('product')->findOrFail($id); // eager load product

        $quantity = intval($request->quantity);
        if ($quantity < 1) $quantity = 1;

        $item->quantity = $quantity;
        $item->save();

        // use actual product price
        $price = $item->product->price ?? 0;
        $itemSubtotal = $price * $quantity;

        $cartTotal = CartItem::with('product')->get()->sum(function($i){
            return ($i->product->price ?? 0) * $i->quantity;
        }) + 50; // shipping

        return response()->json([
            'success' => true,
            'itemSubtotal' => $itemSubtotal,
            'cartTotal' => $cartTotal
        ]);

    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => $e->getMessage(),
        ], 500);
    }
}



// // CartController.php
// public function updateQuantity(Request $request, $id)
// {
//     $cart = Cart::where('user_id', auth()->id())->firstOrFail();
//     $item = $cart->items()->where('id', $id)->firstOrFail();

//     $item->quantity = max(1, $request->quantity);
//     $item->save();

//     $itemSubtotal = $item->price * $item->quantity;
//     $cartSubtotal = $cart->items()->sum(fn($i)=> $i->price * $i->quantity);
//     $cartTotal = $cartSubtotal + 50; // shipping

//     return response()->json([
//         'success'=>true,
//         'itemSubtotal'=>$itemSubtotal,
//         'cartSubtotal'=>$cartSubtotal,
//         'cartTotal'=>$cartTotal
//     ]);
// }



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

//     /**
//      * 💰 Apply Coupon Code
//      */
  public function applyCoupon(Request $request)
    {
        $request->validate([
            'coupon_code' => 'required|string',
        ]);

        $code = $request->coupon_code;

        $coupon = Coupon::where('code', $code)->first();

        if (!$coupon) {
            return response()->json([
                'success' => false,
                'message' => '❌ Invalid coupon code!'
            ]);
        }

        if (!$coupon->is_active) {
            return response()->json([
                'success' => false,
                'message' => '❌ This coupon is inactive!'
            ]);
        }

        if ($coupon->expires_at && now()->gt($coupon->expires_at)) {
            return response()->json([
                'success' => false,
                'message' => '❌ Coupon has expired!'
            ]);
        }

        // Store in session
        Session::put('coupon', [
            'code' => $coupon->code,
            'type' => $coupon->type,
            'value' => $coupon->value,
        ]);

        return response()->json([
            'success' => true,
            'message' => '✅ Coupon applied successfully!',
            'coupon' => [
                'code' => $coupon->code,
                'type' => $coupon->type,
                'value' => $coupon->value
            ]
        ]);
    }

    /**
     * Remove Coupon - AJAX Friendly
     */
    public function removeCoupon(Request $request)
    {
        if (Session::has('coupon')) {
            Session::forget('coupon');
            return response()->json([
                'success' => true,
                'message' => '✅ Coupon removed successfully!'
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => '❌ No coupon applied!'
        ]);
    }
}
