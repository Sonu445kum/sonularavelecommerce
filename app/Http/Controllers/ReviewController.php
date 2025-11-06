<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Product;
use App\Models\Review;
use Illuminate\Support\Facades\Storage;

class ReviewController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth'); // Reviews only for logged-in users
    }

    /**
     * ⭐ Store a newly created review for a product (Amazon/Flipkart style)
     */
    public function store(Request $request, $productId)
    {
        $product = Product::findOrFail($productId);

        // ✅ Validate user input
        $validated = $request->validate([
            'rating'    => 'required|integer|min:1|max:5',
            'comment'   => 'nullable|string|max:2000',
            'images'    => 'nullable|array',
            'images.*'  => 'nullable|file|image|mimes:jpg,jpeg,png,webp|max:5120', // max 5MB per image
            'video'     => 'nullable|file|mimetypes:video/mp4,video/webm|max:51200', // 50MB max
        ]);

        // ✅ Handle multiple image uploads
        $imagePaths = [];
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                if ($image->isValid()) {
                    $imagePaths[] = $image->store('reviews/images', 'public');
                }
            }
        }

        // ✅ Handle optional video upload
        $videoPath = null;
        if ($request->hasFile('video') && $request->file('video')->isValid()) {
            $videoPath = $request->file('video')->store('reviews/videos', 'public');
        }

        // ✅ Create and save the review
        $review = new Review([
            'user_id'     => Auth::id(),
            'product_id'  => $product->id,
            'rating'      => $validated['rating'],
            'comment'     => $validated['comment'] ?? null,
            'images'      => $imagePaths,   // auto JSON via cast
            'video_path'  => $videoPath,
            'is_approved' => true,          // auto-approve reviews
        ]);

        $review->save();

        // ✅ Optional: Update product average rating
        $product->update([
            'average_rating' => $product->reviews()->avg('rating')
        ]);

        // ✅ Redirect back to product page with success message
        return redirect()
            ->route('products.show', $product->slug)
            ->with('success', '✅ Review submitted successfully!')
            ->withFragment('customer-reviews');
    }
}
