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
            $search = trim($req->q);
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // 🏷️ Category filter
        if ($req->filled('category')) {
            $query->whereHas('category', function ($cat) use ($req) {
                $cat->where('slug', $req->category);
            });
        }

        // 💰 Price filter
        if ($req->filled('min_price')) {
            $query->where('price', '>=', (float) $req->min_price);
        }
        if ($req->filled('max_price')) {
            $query->where('price', '<=', (float) $req->max_price);
        }

        // 🔽 Sorting
        switch ($req->get('sort')) {
            case 'price_asc':
                $query->orderBy('price', 'asc');
                break;
            case 'price_desc':
                $query->orderBy('price', 'desc');
                break;
            case 'latest':
            default:
                $query->latest();
                break;
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
        $product = Product::with(['category', 'reviews.user'])
            ->where('slug', $slug)
            ->firstOrFail();

        // 🖼️ Collect all product images
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

        $allImages = array_unique(array_filter($allImages));

        // ✅ Paginate approved reviews
        $reviews = Review::with('user')
            ->where('product_id', $product->id)
            ->where('is_approved', true)
            ->orderBy('created_at', 'desc')
            ->paginate(3);

        // ✅ Related products
        $relatedProducts = Product::where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->where('is_active', true)
            ->latest()
            ->take(4)
            ->get();

        return view('products.show', compact('product', 'relatedProducts', 'allImages', 'reviews'));
    }

    /**
     * ===========================================================
     * 💬 Store Review / Comment / Rating / Feedback
     * ===========================================================
     */
    public function storeReview(Request $req, $productId)
    {
        $product = Product::findOrFail($productId);

        // 🚫 Prevent duplicate reviews
        if (Review::where('product_id', $productId)->where('user_id', auth()->id())->exists()) {
            return back()->with('error', 'You have already submitted a review for this product.');
        }

        // 📝 Validate uploaded files
        $req->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000',
            'images' => 'nullable|array',
            'images.*' => 'file|image|mimes:jpeg,jpg,png,webp|max:2048',
            'video' => 'nullable|file|mimetypes:video/mp4,video/quicktime|max:10240',
            'recorded_video_data' => 'nullable|string',
        ]);

        // 🧩 Create review
        $review = new Review();
        $review->user_id = auth()->id();
        $review->product_id = $productId;
        $review->rating = (int) $req->rating;
        $review->comment = $req->comment ?? null;
        $review->is_approved = false;
        $review->save();

        // 🖼️ Handle multiple image uploads safely
        if ($req->hasFile('images')) {
            $validImages = [];
            foreach ($req->file('images') as $img) {
                if ($img && $img->isValid()) {
                    // ✅ Save to public disk
                    $validImages[] = $img->store('reviews/images', 'public');
                }
            }
            if (!empty($validImages)) {
                $review->images = json_encode($validImages, JSON_UNESCAPED_SLASHES);
                $review->save();
            }
        }

        // 🎞️ Normal video upload
        if ($req->hasFile('video') && $req->file('video')->isValid()) {
            $videoPath = $req->file('video')->store('reviews/videos', 'public');
            $review->video_path = $videoPath;
            $review->save();
        }

        // 🎬 Webcam-recorded video (base64)
        if ($req->filled('recorded_video_data')) {
            $data = $req->recorded_video_data;

            if (preg_match('/^data:video\/(\w+);base64,/', $data, $type)) {
                $videoData = substr($data, strpos($data, ',') + 1);
                $videoData = base64_decode($videoData);

                if ($videoData !== false) {
                    $ext = strtolower($type[1]) ?? 'webm';
                    $fileName = 'reviews/videos/' . uniqid() . '.' . $ext;
                    Storage::disk('public')->put($fileName, $videoData);
                    $review->video_path = $fileName;
                    $review->save();
                }
            }
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
        $query = trim($request->input('query'));

        if (empty($query)) {
            return redirect()->route('products.index');
        }

        $products = Product::where(function ($q) use ($query) {
                $q->where('title', 'LIKE', "%{$query}%")
                  ->orWhere('description', 'LIKE', "%{$query}%");
            })
            ->where('is_active', true)
            ->paginate(12)
            ->withQueryString();

        $categories = Category::where('is_active', true)
                              ->orderBy('name')
                              ->get();

        return view('products.search', compact('products', 'query', 'categories'));
    }
}
