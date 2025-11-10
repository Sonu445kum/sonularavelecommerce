<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    /**
     * MASS ASSIGNABLE FIELDS
     */
    protected $fillable = [
        'category_id',
        'title',
        'slug',
        'description',
        'featured_image',      // Single main product image
        'featured_images',     // Multiple gallery images
        'price',
        'discounted_price',
        'sku',
        'stock',
        'is_active',
        'is_featured',
        'meta',
    ];

    /**
     * TYPE CASTS
     */
    protected $casts = [
        'featured_images' => 'array',
        'meta' => 'array',
        'is_active' => 'boolean',
        'is_featured' => 'boolean',
        'price' => 'decimal:2',
        'discounted_price' => 'decimal:2',
    ];

    /**
     * RELATIONSHIPS
     */

    // Product belongs to a category
    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    // Multiple product images
    public function images()
    {
        return $this->hasMany(ProductImage::class);
    }

// Agar featured image ka alag field hai
public function featuredImage()
{
    return $this->hasOne(ProductImage::class)->where('is_featured', true);
}

    // Alias for backward compatibility (used in blade/controller)
    public function productImages()
    {
        return $this->hasMany(ProductImage::class);
    }

    // Product variants
    public function variants()
    {
        return $this->hasMany(ProductVariant::class);
    }

    // Product reviews
    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    // Appears in multiple cart items
    public function cartItems()
    {
        return $this->hasMany(CartItem::class);
    }

    // Appears in multiple order items
    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * ACCESSORS & HELPERS
     */

    // Final price (discounted or regular)
    public function getFinalPriceAttribute()
    {
        return $this->discounted_price ?? $this->price;
    }

    // Full URL of featured image
    public function getFeaturedImageUrlAttribute()
    {
        if (filter_var($this->featured_image, FILTER_VALIDATE_URL)) {
            return $this->featured_image;
        }
        return $this->featured_image
            ? asset('storage/' . $this->featured_image)
            : asset('images/default-product.jpg');
    }

    // Full URLs for gallery images
    public function getFeaturedGalleryUrlsAttribute()
    {
        if (is_array($this->featured_images)) {
            return array_map(function ($img) {
                return filter_var($img, FILTER_VALIDATE_URL)
                    ? $img
                    : asset('storage/' . $img);
            }, $this->featured_images);
        }
        return [];
    }

    // Average rating
    public function avgRating()
    {
        return $this->reviews()->avg('rating') ?? 0;
    }

    // Stock check
    public function isInStock()
    {
        return $this->stock > 0;
    }

    // Short description for UI
    public function shortDescription($limit = 100)
    {
        return str()->limit(strip_tags($this->description), $limit);
    }
}
