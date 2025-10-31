<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;

class ProductController extends Controller
{
    /**
     * ===========================================================
     * Display all active products with filters and category list
     * ===========================================================
     */
    public function index(Request $req)
    {
        // Base Query: Only active products
        $query = Product::with('category')
                        ->where('is_active', true);

        // 🔍 Search by title or description
        if ($req->filled('q')) {
            $search = $req->q;
            $query->where(function ($sub) use ($search) {
                $sub->where('title', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // 🏷️ Filter by category slug
        if ($req->filled('category')) {
            $query->whereHas('category', function ($cat) use ($req) {
                $cat->where('slug', $req->category);
            });
        }

        // 💰 Filter by price range
        if ($req->filled('min_price')) {
            $query->where('price', '>=', (float) $req->min_price);
        }
        if ($req->filled('max_price')) {
            $query->where('price', '<=', (float) $req->max_price);
        }

        // 🧭 Sorting (optional)
        if ($req->filled('sort')) {
            switch ($req->sort) {
                case 'price_asc':
                    $query->orderBy('price', 'asc');
                    break;
                case 'price_desc':
                    $query->orderBy('price', 'desc');
                    break;
                case 'latest':
                default:
                    $query->latest();
            }
        } else {
            $query->latest();
        }

        // 🧾 Paginate results
        $products = $query->paginate(12)->withQueryString();

        // 📂 Active Categories (for sidebar filters)
        $categories = Category::where('is_active', true)->orderBy('name')->get();

        return view('products.index', compact('products', 'categories'));
    }

    /**
     * ===========================================================
     * Show single product details + related products
     * ===========================================================
     */
    public function show($slug)
    {
        // ✅ Fetch main product
        $product = Product::with('category')->where('slug', $slug)->firstOrFail();

        // 🔁 Related products (same category)
        $relatedProducts = Product::where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->where('is_active', true)
            ->take(4)
            ->get();

        return view('products.show', compact('product', 'relatedProducts'));
    }

    /**
     * ===========================================================
     * 🔍 SEARCH FUNCTIONALITY (Navbar Search Bar)
     * ===========================================================
     */
    public function search(Request $request)
    {
        $query = $request->input('query');

        // Search by title or description
        $products = Product::where('title', 'like', "%{$query}%")
            ->orWhere('description', 'like', "%{$query}%")
            ->where('is_active', true)
            ->paginate(12);

        // Active categories for sidebar
        $categories = Category::where('is_active', true)->orderBy('name')->get();

        // Return the search results view
        return view('products.search', compact('products', 'query', 'categories'));
    }
}
