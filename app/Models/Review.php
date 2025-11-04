<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    use HasFactory;

    /**
     * ✅ The attributes that are mass assignable.
     */
    protected $fillable = [
        'user_id',
        'product_id',
        'rating',
        'comment',
        'images',
        'video_path',   // 🎥 For WebRTC / Uploaded Video
        'is_approved',
    ];

    /**
     * ✅ Cast JSON & boolean fields automatically.
     */
    protected $casts = [
        'images' => 'array',
        'is_approved' => 'boolean',
    ];

    /**
     * ✅ Relationship: Review belongs to a User.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * ✅ Relationship: Review belongs to a Product.
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * 🖼️ Accessor: Get full URLs for review images.
     *
     * Handles:
     * - Local uploaded images (storage path)
     * - External images (like Unsplash URLs)
     * - Single string or JSON array
     */
    public function getImagesAttribute($value)
    {
        if (!$value) return [];

        $decoded = json_decode($value, true);

        // ✅ If JSON array
        if (is_array($decoded)) {
            return array_map(function ($img) {
                if (is_string($img) && (str_starts_with($img, 'http://') || str_starts_with($img, 'https://'))) {
                    return $img; // Keep full URL (e.g., Unsplash)
                }
                return asset('storage/' . ltrim($img, '/')); // Local image path
            }, $decoded);
        }

        // ✅ If single string (not JSON)
        if (is_string($value)) {
            if (str_starts_with($value, 'http://') || str_starts_with($value, 'https://')) {
                return [$value]; // Full URL image
            }
            return [asset('storage/' . ltrim($value, '/'))]; // Local single image
        }

        return [];
    }

    /**
     * 🎬 Accessor: Get full URL for uploaded review video.
     */
    public function getVideoPathAttribute($value)
    {
        return $value ? asset('storage/' . $value) : null;
    }
}
