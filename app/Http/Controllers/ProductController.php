<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;

class ProductController extends Controller
{
    /**
     * ===========================================================
     * 🛍 Display all active products with filters and categories
     * ===========================================================
     */
    public function index(Request $req)
    {
        // ✅ Base Query - Active products + Category relation
        $query = Product::with('category')
                        ->where('is_active', true);

        // 🔍 Search filter
        if ($req->filled('q')) {
            $search = $req->q;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // 🏷️ Category filter (slug-based)
        if ($req->filled('category')) {
            $query->whereHas('category', function ($cat) use ($req) {
                $cat->where('slug', $req->category);
            });
        }

        // 💰 Price range filter
        if ($req->filled('min_price')) {
            $query->where('price', '>=', (float)$req->min_price);
        }
        if ($req->filled('max_price')) {
            $query->where('price', '<=', (float)$req->max_price);
        }

        // 🔽 Sorting filter
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

        // 🧾 Paginate + preserve filters
        $products = $query->paginate(12)->withQueryString();

        // 📂 Active Categories for Sidebar Filters
        $categories = Category::where('is_active', true)
                              ->orderBy('name')
                              ->get();

        return view('products.index', compact('products', 'categories'));
    }

    /**
     * ===========================================================
     * 🎯 Show single product details + related products
     * ===========================================================
     */
    public function show($slug)
    {
        // ✅ Get main product
        $product = Product::with('category')->where('slug', $slug)->firstOrFail();

        // 🔁 Related Products (Same category)
        $relatedProducts = Product::where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->where('is_active', true)
            ->latest()
            ->take(4)
            ->get();

        return view('products.show', compact('product', 'relatedProducts'));
    }

    /**
     * ===========================================================
     * 🔎 Navbar Search Functionality
     * ===========================================================
     */
    public function search(Request $request)
{
    $query = $request->input('query');

    $products = Product::where('title', 'LIKE', "%{$query}%")
        ->orWhere('description', 'LIKE', "%{$query}%")
        ->where('is_active', true)
        ->paginate(12);

    $categories = Category::where('is_active', true)->orderBy('name')->get();

    return view('products.search', compact('products', 'query', 'categories'));
}

}
