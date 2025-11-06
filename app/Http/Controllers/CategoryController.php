<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

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
     * 👨‍💼 ADMIN INDEX METHOD
     * ----------------------------------------------------
     * Displays all categories for admin management
     */
    public function adminIndex()
    {
        $categories = Category::with('children')->latest()->paginate(20);
        return view('admin.categories.index', compact('categories'));
    }

    /**
     * ----------------------------------------------------
     * ➕ CREATE METHOD
     * ----------------------------------------------------
     * Shows the form to create a new category
     */
    public function create()
    {
        $categories = Category::whereNull('parent_id')->get();
        return view('admin.categories.create', compact('categories'));
    }

    /**
     * ----------------------------------------------------
     * ✏️ EDIT METHOD
     * ----------------------------------------------------
     * Shows the form to edit an existing category
     */
    public function edit($id)
    {
        $category = Category::findOrFail($id);
        $categories = Category::whereNull('parent_id')
            ->where('id', '!=', $id)
            ->get();

        return view('admin.categories.edit', compact('category', 'categories'));
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
            'slug' => 'nullable|string|max:255|unique:categories,slug',
            'parent_id' => 'nullable|exists:categories,id',
            'description' => 'nullable|string',
        ]);

        // ✅ Auto-generate unique slug if not provided
        if (empty($data['slug'])) {
            $baseSlug = Str::slug($data['name']);
            $slug = $baseSlug;
            $i = 1;

            while (Category::where('slug', $slug)->exists()) {
                $slug = $baseSlug . '-' . $i++;
            }

            $data['slug'] = $slug;
        }

        Category::create($data);

        return redirect()
            ->route('admin.categories.index')
            ->with('success', '✅ Category created successfully!');
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
            'slug' => "nullable|string|max:255|unique:categories,slug,{$category->id}",
            'parent_id' => 'nullable|exists:categories,id',
            'description' => 'nullable|string',
        ]);

        // ✅ Auto-generate slug if left blank
        if (empty($data['slug'])) {
            $baseSlug = Str::slug($data['name']);
            $slug = $baseSlug;
            $i = 1;

            while (Category::where('slug', $slug)
                ->where('id', '!=', $category->id)
                ->exists()) {
                $slug = $baseSlug . '-' . $i++;
            }

            $data['slug'] = $slug;
        }

        $category->update($data);

        return redirect()
            ->route('admin.categories.index')
            ->with('success', '✅ Category updated successfully!');
    }

    /**
     * ----------------------------------------------------
     * 🗑️ DESTROY METHOD
     * ----------------------------------------------------
     * Deletes a category and all its child categories.
     */
    public function destroy(Category $category)
    {
        try {
            DB::transaction(function () use ($category) {
                $this->deleteSubcategories($category);
                $category->delete();
            });

            // Optional cache cleanup
            cache()->forget('categories_list');

            // ✅ Check if the request came via AJAX
            if (request()->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => '🗑️ Category deleted successfully!',
                ]);
            }

            // ✅ Normal delete (form POST)
            return redirect()
                ->route('admin.categories.index')
                ->with('success', '🗑️ Category deleted successfully!');

        } catch (\Exception $e) {
            // Handle errors for both JSON & redirect requests
            if (request()->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => '⚠️ Error deleting category: ' . $e->getMessage(),
                ], 500);
            }

            return back()->with('error', '⚠️ Error deleting category: ' . $e->getMessage());
        }
    }

    /**
     * ✅ Helper to delete nested subcategories
     */
    private function deleteSubcategories(Category $category)
    {
        if ($category->children()->exists()) {
            foreach ($category->children as $child) {
                $this->deleteSubcategories($child);
                $child->delete();
            }
        }
    }
}
