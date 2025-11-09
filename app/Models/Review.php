<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'product_id',
        'rating',
        'comment',
        'images',
        'video_path',
        'is_approved',
    ];

    protected $casts = [
        'images' => 'array',
        'is_approved' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * ✅ Get clean URLs for images
     */
    public function getImagesAttribute($value)
    {
        if (empty($value)) return [];

        // Decode JSON if stored as string
        $images = is_array($value) ? $value : json_decode($value, true);
        if (!is_array($images)) $images = [$value];

        // ✅ Return clean URLs
        return array_map(function ($img) {
            if (empty($img)) return null;

            // If it's already a full URL, return as-is
            if (str_starts_with($img, 'http://') || str_starts_with($img, 'https://')) {
                return $img;
            }

            // Otherwise prepend storage path
            return asset('storage/' . ltrim($img, '/'));
        }, $images);
    }

    /**
     * ✅ Get clean URL for video
     */
    public function getVideoPathAttribute($value)
    {
        if (empty($value)) return null;

        if (str_starts_with($value, 'http://') || str_starts_with($value, 'https://')) {
            return $value;
        }

        return asset('storage/' . ltrim($value, '/'));
    }
}
