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
     * 🏷️ Show all categories or products in category
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
     * 👨‍💼 Admin - Show all categories
     */
    public function adminIndex()
    {
        $categories = Category::with('children')->latest()->paginate(20);
        return view('admin.categories.index', compact('categories'));
    }

    /**
     * ➕ Show create form
     */
    public function create()
    {
        $categories = Category::whereNull('parent_id')->get();
        return view('admin.categories.create', compact('categories'));
    }

    /**
     * ✏️ Show edit form
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
     * 🛍️ Show category or all products
     */
    public function show($slug)
    {
        if ($slug === 'all') {
            $categoryName = 'All Products';
            $products = Product::where('is_active', true)->latest()->paginate(12);
        } else {
            $category = Category::where('slug', $slug)->firstOrFail();
            $categoryName = $category->name;
            $products = $category->products()->where('is_active', true)->paginate(12);
        }

        return view('categories.show', compact('products', 'categoryName'));
    }

    /**
     * ✅ STORE - Create new category
     */
    public function store(Request $req)
    {
        $data = $req->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255',
            'parent_id' => 'nullable|exists:categories,id',
            'description' => 'nullable|string',
        ]);

        // 🔹 Handle slug logic
        if (!empty($data['slug'])) {
            $baseSlug = Str::slug($data['slug']);
        } else {
            $baseSlug = Str::slug($data['name']);
        }

        // Always ensure unique slug
        $slug = $this->generateUniqueSlug($baseSlug);
        $data['slug'] = $slug;

        Category::create($data);

        return redirect()
            ->route('admin.categories.index')
            ->with('success', '✅ Category created successfully!');
    }

    /**
     * ✅ UPDATE - Update category
     */
    public function update(Request $req, Category $category)
    {
        $data = $req->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255',
            'parent_id' => 'nullable|exists:categories,id',
            'description' => 'nullable|string',
        ]);

        // 🔹 If user updated slug manually
        if (!empty($data['slug'])) {
            $baseSlug = Str::slug($data['slug']);
        } else {
            $baseSlug = Str::slug($data['name']);
        }

        // Ensure unique slug (ignore current ID)
        $slug = $this->generateUniqueSlug($baseSlug, $category->id);
        $data['slug'] = $slug;

        $category->update($data);

        return redirect()
            ->route('admin.categories.index')
            ->with('success', '✅ Category updated successfully!');
    }

    /**
     * 🗑️ Delete category + subcategories
     */
    public function destroy(Category $category)
    {
        try {
            DB::transaction(function () use ($category) {
                $this->deleteSubcategories($category);
                $category->delete();
            });

            cache()->forget('categories_list');

            if (request()->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => '🗑️ Category deleted successfully!',
                ]);
            }

            return redirect()
                ->route('admin.categories.index')
                ->with('success', '🗑️ Category deleted successfully!');

        } catch (\Exception $e) {
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
     * ♻️ Recursive delete for subcategories
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

    /**
     * 🧠 Helper - Generate unique slug
     */
    private function generateUniqueSlug($baseSlug, $ignoreId = null)
    {
        $slug = $baseSlug;
        $i = 1;

        while (
            Category::where('slug', $slug)
                ->when($ignoreId, fn($q) => $q->where('id', '!=', $ignoreId))
                ->exists()
        ) {
            $slug = $baseSlug . '-' . $i++;
        }

        return $slug;
    }
}
