<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;
use App\Models\Review;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    /**
     * ===========================================================
     * 🛍 Display all active products with filters and categories
     * ===========================================================
     */
    public function index(Request $req)
    {
        $query = Product::with('category')->where('is_active', true);

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

        $products = $query->paginate(12)->withQueryString();

        $categories = Category::where('is_active', true)
                              ->orderBy('name')
                              ->get();

        return view('products.index', compact('products', 'categories'));
    }

    /**
     * ===========================================================
     * 🎯 Show single product details + related products + reviews
     * ===========================================================
     */
    public function show($slug)
    {
        $product = Product::with([
            'category',
            'reviews.user',
            'images'
        ])->where('slug', $slug)->firstOrFail();

        // 🖼️ Collect all images (featured + gallery)
        $allImages = [];

        if (!empty($product->featured_image)) {
            $allImages[] = $product->featured_image;
        }

        if (!empty($product->featured_images)) {
            $featuredArray = is_array($product->featured_images)
                ? $product->featured_images
                : json_decode($product->featured_images, true);

            if (is_array($featuredArray)) {
                $allImages = array_merge($allImages, $featuredArray);
            }
        }

        if ($product->images && $product->images->count() > 0) {
            $allImages = array_merge($allImages, $product->images->pluck('url')->toArray());
        }

        $allImages = array_unique($allImages);

        // 🔁 Related Products
        $relatedProducts = Product::where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->where('is_active', true)
            ->latest()
            ->take(4)
            ->get();

        // 🧩 Reviews (Approved only + Paginated)
        $reviews = Review::with('user')
            ->where('product_id', $product->id)
            ->where('is_approved', true)
            ->orderBy('created_at', 'desc')
            ->paginate(5); // 5 reviews per page

        return view('products.show', compact('product', 'relatedProducts', 'allImages', 'reviews'));
    }

    /**
     * ===========================================================
     * 💬 Store Review / Comment / Rating / Feedback
     * ===========================================================
     */
    public function storeReview(Request $req, $productId)
    {
        $req->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000',
            'images.*' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
            'video' => 'nullable|file|mimetypes:video/mp4,video/quicktime|max:20480', // 20MB
        ]);

        $product = Product::findOrFail($productId);

        // ✅ Check if user already reviewed this product
        $existing = Review::where('product_id', $productId)
                          ->where('user_id', Auth::id())
                          ->first();

        if ($existing) {
            return back()->with('error', 'You have already submitted a review for this product.');
        }

        // 🧩 Create new review record
        $review = new Review();
        $review->user_id = Auth::id();
        $review->product_id = $productId;
        $review->rating = $req->rating;
        $review->comment = $req->comment ?? null;
        $review->is_approved = false; // Wait for admin approval
        $review->save();

        // 🖼️ Handle multiple images upload
        $paths = [];
        if ($req->hasFile('images')) {
            foreach ($req->file('images') as $file) {
                $paths[] = $file->store('reviews', 'public'); // Store in storage/app/public/reviews
            }
            $review->images = json_encode($paths); // ✅ Proper JSON encoding
            $review->save();
        }

        // 🎥 Handle single video upload (WebRTC)
        if ($req->hasFile('video')) {
            $videoPath = $req->file('video')->store('review_videos', 'public');
            $review->video_path = $videoPath;
            $review->save();
        }

        return back()->with('success', '✅ Your review has been submitted successfully and is awaiting admin approval.');
    }

    /**
     * ===========================================================
     * 🔎 Navbar Search Functionality
     * ===========================================================
     */
    public function search(Request $request)
    {
        $query = $request->input('query');

        $products = Product::where(function ($q) use ($query) {
            $q->where('title', 'LIKE', "%{$query}%")
              ->orWhere('description', 'LIKE', "%{$query}%");
        })
        ->where('is_active', true)
        ->paginate(12);

        $categories = Category::where('is_active', true)
                              ->orderBy('name')
                              ->get();

        return view('products.search', compact('products', 'query', 'categories'));
    }
}
