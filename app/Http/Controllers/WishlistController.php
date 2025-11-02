<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Wishlist;
use App\Models\Product;

class WishlistController extends Controller
{
    /**
     * ==========================
     * Constructor: Middleware
     * ==========================
     */
    public function __construct()
    {
        $this->middleware('auth')->except(['adminIndex']);
        $this->middleware('admin')->only(['adminIndex', 'adminRemove']);
    }

    /**
     * ========================================
     * USER PANEL — SHOW ALL WISHLIST PRODUCTS
     * ========================================
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        // Fetch user's wishlist with product details
        $items = Wishlist::with(['product.images'])
            ->where('user_id', $user->id)
            ->latest()
            ->paginate(20);

        // Handle AJAX request
        if ($request->ajax()) {
            return response()->json([
                'status' => true,
                'data' => $items,
            ]);
        }

        // Return Blade view
        return view('wishlist.index', compact('items'));
    }

    /**
     * ========================================
     * USER PANEL — ADD PRODUCT TO WISHLIST
     * ========================================
     */
    public function add(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
        ]);

        $userId = Auth::id();

        $wishlistItem = Wishlist::firstOrCreate([
            'user_id' => $userId,
            'product_id' => $validated['product_id'],
        ]);

        // Handle AJAX request
        if ($request->ajax()) {
            return response()->json([
                'status' => true,
                'message' => $wishlistItem->wasRecentlyCreated
                    ? 'Product added to your wishlist 💖'
                    : 'This product is already in your wishlist.',
            ]);
        }

        // Normal Blade redirect
        return back()->with(
            $wishlistItem->wasRecentlyCreated ? 'success' : 'info',
            $wishlistItem->wasRecentlyCreated
                ? 'Product added to your wishlist 💖'
                : 'This product is already in your wishlist.'
        );
    }

    /**
     * ========================================
     * USER PANEL — REMOVE PRODUCT FROM WISHLIST
     * ========================================
     */
    public function remove(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
        ]);

        $deleted = Wishlist::where('user_id', Auth::id())
            ->where('product_id', $validated['product_id'])
            ->delete();

        if ($request->ajax()) {
            return response()->json([
                'status' => (bool) $deleted,
                'message' => $deleted
                    ? 'Product removed from wishlist 🗑️'
                    : 'Item not found in wishlist.',
            ]);
        }

        return back()->with(
            $deleted ? 'success' : 'error',
            $deleted
                ? 'Product removed from your wishlist 🗑️'
                : 'Item not found in wishlist.'
        );
    }

    /**
     * ========================================
     * ADMIN PANEL — VIEW ALL WISHLISTS
     * (Search, Pagination, AJAX Support)
     * ========================================
     */
    public function adminIndex(Request $request)
    {
        $search = $request->get('search');

        $wishlists = Wishlist::with(['user', 'product'])
            ->when($search, function ($query) use ($search) {
                $query->whereHas('user', fn($q) => $q->where('name', 'like', "%$search%"))
                      ->orWhereHas('product', fn($q) => $q->where('name', 'like', "%$search%"));
            })
            ->latest()
            ->paginate(20);

        // For AJAX table updates
        if ($request->ajax()) {
            return response()->json([
                'status' => true,
                'data' => $wishlists,
            ]);
        }

        return view('admin.wishlist.index', compact('wishlists', 'search'));
    }

    /**
     * ========================================
     * ADMIN PANEL — DELETE ANY WISHLIST ENTRY
     * ========================================
     */
    public function adminRemove($id, Request $request)
    {
        $wishlist = Wishlist::find($id);

        if (!$wishlist) {
            if ($request->ajax()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Wishlist entry not found ⚠️',
                ]);
            }

            return back()->with('error', 'Wishlist entry not found ⚠️');
        }

        $wishlist->delete();

        if ($request->ajax()) {
            return response()->json([
                'status' => true,
                'message' => 'Wishlist entry deleted successfully ✅',
            ]);
        }

        return back()->with('success', 'Wishlist entry deleted successfully ✅');
    }
}
