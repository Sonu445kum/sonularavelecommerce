<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Wishlist;
use Illuminate\Http\Request;

class AdminWishlistController extends Controller
{
    // 💖 Show all wishlist items (admin view)
    public function index()
    {
        $wishlists = Wishlist::with(['user', 'product.images'])->latest()->paginate(20);
        return view('admin.wishlist.index', compact('wishlists'));
    }

    // 🗑️ Delete wishlist item
    public function destroy($id)
    {
        $wishlist = Wishlist::findOrFail($id);
        $wishlist->delete();

        return redirect()->route('admin.wishlist.index')
                         ->with('success', 'Wishlist item removed successfully!');
    }
}
