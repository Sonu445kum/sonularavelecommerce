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
     * ⭐ Store a newly created review for a product
     */
    public function store(Request $request, $productId)
    {
        $product = Product::findOrFail($productId);

        // =====================================
        // 🧹 Step 1: Clean invalid/empty images
        // =====================================
        if ($request->has('images')) {
            $cleaned = [];

            foreach ($request->file('images', []) as $file) {
                if ($file && $file->isValid()) {
                    $cleaned[] = $file;
                }
            }

            $request->files->set('images', $cleaned);
        }

        // ===============================
        // ✅ Validation Rules
        // ===============================
        $validated = $request->validate([
            'rating'               => 'required|integer|min:1|max:5',
            'comment'              => 'nullable|string|max:2000',

            'images'               => 'nullable|array',
            'images.*'             => 'sometimes|file|mimes:jpg,jpeg,png,webp|max:5120',

            'video'                => 'nullable|file|mimes:mp4,webm,ogg|max:51200',
            'recorded_video_data'  => 'nullable|string',
        ]);

        // ===============================
        // 🖼️ Handle Multiple Image Uploads
        // ===============================
        $imagePaths = [];

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $file) {
                if ($file->isValid()) {
                    // Store file in public disk
                    $path = $file->store('reviews/images', 'public');
                    // Normalize slashes and push to array
                    $imagePaths[] = str_replace('\\', '/', $path);
                }
            }
        }

        // ===============================
        // 🎥 Handle Video Uploads
        // ===============================
        $videoPath = null;

        // ✅ Normal video upload
        if ($request->hasFile('video') && $request->file('video')->isValid()) {
            $videoPath = $request->file('video')->store('reviews/videos', 'public');
            $videoPath = str_replace('\\', '/', $videoPath);
        }

        // ✅ Recorded video (Base64 data)
        if ($request->filled('recorded_video_data')) {
            $videoData = $request->input('recorded_video_data');

            if (preg_match('/^data:video\/(\w+);base64,/', $videoData, $matches)) {
                $extension = strtolower($matches[1]);
                $videoData = substr($videoData, strpos($videoData, ',') + 1);
                $decoded = base64_decode($videoData);

                if ($decoded !== false) {
                    $filename = 'reviews/videos/' . uniqid('recorded_', true) . '.' . $extension;
                    Storage::disk('public')->put($filename, $decoded);
                    $videoPath = str_replace('\\', '/', $filename);
                }
            }
        }

        // ===============================
        // 💾 Save Review
        // ===============================
        $review = new Review();
        $review->user_id     = Auth::id();
        $review->product_id  = $product->id;
        $review->rating      = $validated['rating'];
        $review->comment     = $validated['comment'] ?? null;

        // ✅ Fix JSON encoding (no escaped slashes)
        $review->images = !empty($imagePaths) ? $imagePaths : null;

        $review->video_path  = $videoPath;
        $review->is_approved = true;
        $review->save();

        // ===============================
        // ⭐ Update Product’s Average Rating
        // ===============================
        $product->update([
            'average_rating' => round($product->reviews()->avg('rating'), 1)
        ]);

        // ===============================
        // 🔁 Redirect Back
        // ===============================
        return redirect()
            ->route('products.show', $product->slug)
            ->with('success', '✅ Review submitted successfully!')
            ->withFragment('customer-reviews');
    }

    /**
     * ⭐ Reorder Reviews (Admin / Logged-in User)
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
