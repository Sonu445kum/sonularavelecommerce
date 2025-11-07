<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\Product;
use App\Models\Review;

class ReviewController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth'); // Only logged-in users can review
    }

    /**
     * ⭐ Store a newly created review for a product (Amazon/Flipkart style)
     */
    public function store(Request $request, $productId)
    {
        $product = Product::findOrFail($productId);

        // ✅ Validate review data
        $validated = $request->validate([
            'rating'         => 'required|integer|min:1|max:5',
            'comment'        => 'nullable|string|max:2000',
            'images'         => 'nullable|array',
            'images.*'       => 'nullable|file|image|mimes:jpg,jpeg,png,webp|max:5120', // 5MB max
            'video'          => 'nullable|file|mimetypes:video/mp4,video/webm,video/ogg|max:51200', // 50MB max
            'recorded_video' => 'nullable|string', // base64 (if recorded)
        ]);

        // ====================================
        // 🖼️ Handle Multiple Image Uploads
        // ====================================
        $imagePaths = [];

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                if ($image->isValid()) {
                    // store() automatically saves in storage/app/public/reviews/images
                    $path = $image->store('reviews/images', 'public');
                    $imagePaths[] = $path; // Example: "reviews/images/abc123.jpg"
                }
            }
        }

        // ====================================
        // 🎥 Handle Video Uploads (File + Recorded)
        // ====================================
        $videoPath = null;

        // Uploaded video (normal file upload)
        if ($request->hasFile('video') && $request->file('video')->isValid()) {
            $videoPath = $request->file('video')->store('reviews/videos', 'public');
        }

        // Recorded video (base64 string)
        if ($request->filled('recorded_video')) {
            $videoData = $request->input('recorded_video');
            if (preg_match('/^data:video\/(\w+);base64,/', $videoData, $matches)) {
                $extension = $matches[1];
                $videoData = substr($videoData, strpos($videoData, ',') + 1);
                $videoData = base64_decode($videoData);

                $filename = 'reviews/videos/' . uniqid('recorded_', true) . '.' . $extension;
                Storage::disk('public')->put($filename, $videoData);
                $videoPath = $filename;
            }
        }

        // ====================================
        // 💾 Save Review in Database
        // ====================================
        $review = new Review();
        $review->user_id     = Auth::id();
        $review->product_id  = $product->id;
        $review->rating      = $validated['rating'];
        $review->comment     = $validated['comment'] ?? null;
        $review->images      = !empty($imagePaths) ? json_encode($imagePaths) : null;
        $review->video_path  = $videoPath;
        $review->is_approved = true; // auto approve (optional)

        $review->save();

        // ====================================
        // ⭐ Update Product’s Average Rating
        // ====================================
        $product->update([
            'average_rating' => round($product->reviews()->avg('rating'), 1)
        ]);

        // ====================================
        // ✅ Redirects After Success
        // ====================================
        if ($request->has('order_id')) {
            return redirect()
                ->route('orders.show', $request->input('order_id'))
                ->with('success', '✅ Review submitted successfully!');
        }

        return redirect()
            ->route('products.show', $product->slug)
            ->with('success', '✅ Review submitted successfully!')
            ->withFragment('customer-reviews');
    }

    /**
     * ⭐ Reorder Reviews (For Admin or Logged-in User)
     */
    public function reorder(Request $request, Product $product)
    {
        $order = $request->input('order', []);

        foreach ($order as $item) {
            Review::where('id', $item['id'])->update(['position' => $item['position']]);
        }

        return response()->json([
            'success' => true,
            'message' => '✅ Reviews reordered successfully!'
        ]);
    }
}
