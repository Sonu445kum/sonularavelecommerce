<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Support\Facades\Storage;

class AdminProductController extends Controller
{
    /**
     * Display a listing of products for admin.
     */
    public function adminIndex()
    {
        $products = Product::with('category')->paginate(20);
        return view('admin.products.index', compact('products'));
    }

    public function index()
    {
        return $this->adminIndex();
    }

    public function create() { $categories = Category::all(); return view('admin.products.create', compact('categories')); }

    public function store(Request $req)
    {
        $rules = [
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|unique:products,slug',
            'description' => 'nullable|string',
            'category_id' => 'nullable|exists:categories,id',
            'price' => 'required|numeric|min:0',
            'discounted_price' => 'nullable|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'sku' => 'nullable|string|max:255',
            'is_active' => 'nullable|boolean',
            'is_featured' => 'nullable|boolean',
        ];

        // Add image validation - only validate if file is actually uploaded and valid
        // Check featured_image upload error code (UPLOAD_ERR_NO_FILE = 4 means no file)
        if ($req->hasFile('featured_image')) {
            $featuredImage = $req->file('featured_image');
            if ($featuredImage && $featuredImage->getError() !== UPLOAD_ERR_NO_FILE && $featuredImage->isValid()) {
                $rules['featured_image'] = 'image|mimes:jpg,jpeg,png|max:2048';
            }
        }

        // Check images array - only validate valid files
        if ($req->hasFile('images')) {
            $hasValidImages = false;
            foreach ($req->file('images') as $file) {
                if ($file && $file->getError() !== UPLOAD_ERR_NO_FILE && $file->isValid()) {
                    $hasValidImages = true;
                    break;
                }
            }
            if ($hasValidImages) {
                $rules['images.*'] = 'image|mimes:jpg,jpeg,png|max:2048';
            }
        }

        $data = $req->validate($rules);

        // Auto-generate slug from title if not provided
        if (empty($data['slug']) && !empty($data['title'])) {
            $data['slug'] = \Illuminate\Support\Str::slug($data['title']);
            // Ensure uniqueness
            $originalSlug = $data['slug'];
            $counter = 1;
            while (Product::where('slug', $data['slug'])->exists()) {
                $data['slug'] = $originalSlug . '-' . $counter;
                $counter++;
            }
        }

        // Handle featured image
        if ($req->hasFile('featured_image')) {
            $data['featured_image'] = $req->file('featured_image')->store('products', 'public');
        }

        // Convert checkbox values to boolean
        $data['is_active'] = $req->has('is_active') ? true : false;
        $data['is_featured'] = $req->has('is_featured') ? true : false;

        $product = Product::create($data);

        // Handle additional images (only process valid files)
        if ($req->hasFile('images')) {
            foreach ($req->file('images') as $file) {
                if ($file && $file->isValid() && $file->getError() === UPLOAD_ERR_OK) {
                    $path = $file->store('products', 'public');
                    $product->images()->create(['path' => $path]);
                }
            }
        }

        return redirect()->route('admin.products.index')->with('success', 'Product created successfully!');
    }

    public function edit(Product $product) { $categories = Category::all(); return view('admin.products.edit', compact('product','categories')); }

    public function update(Request $req, Product $product)
    {
        $data = $req->validate(['title'=>'required','slug'=>"required|unique:products,slug,{$product->id}",'price'=>'required|numeric','stock'=>'required|integer']);
        $product->update($data);
        return back()->with('success','Updated');
    }

    public function destroy(Product $product)
    {
        foreach ($product->images as $img) Storage::disk('public')->delete($img->path);
        $product->delete();
        return back()->with('success','Deleted');
    }
}