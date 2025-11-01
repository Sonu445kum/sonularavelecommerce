<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Product;

class CategoryController extends Controller
{
    /**
     * ----------------------------------------------------
     * 🏷️ INDEX METHOD
     * ----------------------------------------------------
     * Displays either:
     *  - All categories (if no slug passed)
     *  - Products inside a specific category (if slug passed)
     */
    public function index(Request $req, $slug = null)
    {
        if ($slug) {
            $category = Category::where('slug', $slug)->firstOrFail();
            $products = $category->products()->paginate(12);

            return view('categories.show', [
                'category' => $category,
                'products' => $products,
                'categoryName' => $category->name,
            ]);
        }

        $categories = Category::with('children')->get();
        return view('categories.index', compact('categories'));
    }

    /**
     * ----------------------------------------------------
     * 🛍️ SHOW METHOD
     * ----------------------------------------------------
     * Handles:
     *  - "Shop Now" button → shows all products
     *  - Category details page → products by category
     */
    public function show($slug)
    {
        if ($slug === 'all') {
            $categoryName = 'All Products';
            $products = Product::where('is_active', true)
                ->latest()
                ->paginate(12);
        } else {
            $category = Category::where('slug', $slug)->firstOrFail();
            $categoryName = $category->name;
            $products = $category->products()
                ->where('is_active', true)
                ->paginate(12);
        }

        return view('categories.show', compact('products', 'categoryName'));
    }

    /**
     * ----------------------------------------------------
     * ➕ STORE METHOD
     * ----------------------------------------------------
     * Creates a new category (used in admin panel)
     */
    public function store(Request $req)
    {
        $data = $req->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:categories,slug',
            'parent_id' => 'nullable|exists:categories,id',
            'description' => 'nullable|string',
        ]);

        Category::create($data);

        return back()->with('success', '✅ Category created successfully!');
    }

    /**
     * ----------------------------------------------------
     * ✏️ UPDATE METHOD
     * ----------------------------------------------------
     * Updates existing category data.
     */
    public function update(Request $req, Category $category)
    {
        $data = $req->validate([
            'name' => 'required|string|max:255',
            'slug' => "required|string|max:255|unique:categories,slug,{$category->id}",
            'parent_id' => 'nullable|exists:categories,id',
            'description' => 'nullable|string',
        ]);

        $category->update($data);

        return back()->with('success', '✅ Category updated successfully!');
    }

    /**
     * ----------------------------------------------------
     * 🗑️ DESTROY METHOD
     * ----------------------------------------------------
     * Deletes a category and all its child categories.
     */
    public function destroy(Category $category)
    {
        // Optional: delete subcategories first
        if ($category->children()->count() > 0) {
            $category->children()->delete();
        }

        $category->delete();

        return back()->with('success', '🗑️ Category deleted successfully!');
    }
}
