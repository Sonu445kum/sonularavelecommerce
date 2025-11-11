<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class AdminProductController extends Controller
{
    /**
     * 🏷️ Display all products for admin
     */
    public function adminIndex()
    {
        $products = Product::with('category')->latest()->paginate(20);
        return view('admin.products.index', compact('products'));
    }

    public function index()
    {
        return $this->adminIndex();
    }

    /**
     * 🆕 Show product create form
     */
    public function create()
    {
        $categories = Category::all();
        return view('admin.products.create', compact('categories'));
    }

    /**
     * 💾 Store product
     */
    public function store(Request $req)
{
    // Step 1️⃣ Basic validation (without forcing image fields)
    $rules = [
        'title' => 'required|string|max:255',
        'slug' => 'nullable|string|max:255|unique:products,slug',
        'description' => 'nullable|string',
        'category_id' => 'nullable|exists:categories,id',
        'price' => 'required|numeric|min:0',
        'discounted_price' => 'nullable|numeric|min:0',
        'stock' => 'required|integer|min:0',
        'sku' => 'nullable|string|max:255',
        'is_active' => 'nullable|boolean',
        'is_featured' => 'nullable|boolean',
    ];

    // Step 2️⃣ Only add image validation if file uploaded
    if ($req->hasFile('featured_image') && $req->file('featured_image')->isValid()) {
        $rules['featured_image'] = 'image|mimes:jpg,jpeg,png,webp|max:5120';
    }

    if ($req->hasFile('images')) {
        foreach ($req->file('images') as $img) {
            if ($img && $img->isValid()) {
                $rules['images.*'] = 'image|mimes:jpg,jpeg,png,webp|max:5120';
                break; // only validate once if any valid file exists
            }
        }
    }

    $data = $req->validate($rules);

    // 🧩 Auto-slug
    if (empty($data['slug'])) {
        $data['slug'] = \Illuminate\Support\Str::slug($data['title']);
        $baseSlug = $data['slug'];
        $i = 1;
        while (Product::where('slug', $data['slug'])->exists()) {
            $data['slug'] = "{$baseSlug}-{$i}";
            $i++;
        }
    }

    // 📸 Handle featured image
    if ($req->hasFile('featured_image') && $req->file('featured_image')->isValid()) {
        $data['featured_image'] = $req->file('featured_image')->store('products', 'public');
    }

    // 🖼️ Handle gallery images
    $gallery = [];
    if ($req->hasFile('images')) {
        foreach ($req->file('images') as $img) {
            if ($img && $img->isValid()) {
                $gallery[] = $img->store('products/gallery', 'public');
            }
        }
    }
    $data['images'] = json_encode($gallery);

    // ✅ Booleans
    $data['is_active'] = $req->has('is_active');
    $data['is_featured'] = $req->has('is_featured');

    // 💾 Create Product
    Product::create($data);

    return redirect()->route('admin.products.index')->with('success', '✅ Product created successfully!');
}


    /**
     * ✏️ Edit product
     */
    public function edit(Product $product)
    {
        $categories = Category::all();
        return view('admin.products.edit', compact('product', 'categories'));
    }

    /**
     * 🔁 Update product
     */
    public function update(Request $req, Product $product)
{
    $rules = [
        'title' => 'required|string|max:255',
        'slug' => 'nullable|string|max:255|unique:products,slug,' . $product->id,
        'description' => 'nullable|string',
        'category_id' => 'nullable|exists:categories,id',
        'price' => 'required|numeric|min:0',
        'discounted_price' => 'nullable|numeric|min:0', // ✅ validate discounted_price
        'stock' => 'required|integer|min:0',
        'sku' => 'nullable|string|max:255',
        'featured_image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
        'images.*' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
        'is_active' => 'nullable|boolean',
        'is_featured' => 'nullable|boolean',
    ];

    $data = $req->validate($rules);

    // Auto-slug if missing
    if (empty($data['slug']) && !empty($data['title'])) {
        $data['slug'] = Str::slug($data['title']);
    }

    // Update featured image
    if ($req->hasFile('featured_image') && $req->file('featured_image')->isValid()) {
        if ($product->featured_image && Storage::disk('public')->exists($product->featured_image)) {
            Storage::disk('public')->delete($product->featured_image);
        }
        $data['featured_image'] = $req->file('featured_image')->store('products', 'public');
    }

    // Update multiple images (append new)
    $existingImages = json_decode($product->images, true) ?? [];
    if ($req->hasFile('images')) {
        foreach ($req->file('images') as $file) {
            if ($file->isValid()) {
                $existingImages[] = $file->store('products/gallery', 'public');
            }
        }
    }
    $data['images'] = json_encode($existingImages);

    // ✅ Ensure booleans are properly set
    $data['is_active'] = $req->has('is_active');
    $data['is_featured'] = $req->has('is_featured');

    // 💾 Update product
    $product->update($data);

    return redirect()->route('admin.products.index')->with('success', '✅ Product updated successfully!');
}


    /**
     * 🗑️ Delete product
     */
    public function destroy(Product $product)
    {
        try {
            DB::transaction(function () use ($product) {
                if ($product->featured_image && Storage::disk('public')->exists($product->featured_image)) {
                    Storage::disk('public')->delete($product->featured_image);
                }

                $images = json_decode($product->images, true);
                if ($images) {
                    foreach ($images as $img) {
                        if (Storage::disk('public')->exists($img)) {
                            Storage::disk('public')->delete($img);
                        }
                    }
                }

                $product->delete();
            });

            return back()->with('success', '🗑️ Product deleted successfully!');
        } catch (\Exception $e) {
            return back()->with('error', '⚠️ Error deleting product: ' . $e->getMessage());
        }
    }
}
