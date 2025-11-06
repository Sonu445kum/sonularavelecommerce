<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'parent_id',
    ];

    /**
     * ----------------------------------------------------
     * ⚙️ Boot Method — Auto handle slug & relationships
     * ----------------------------------------------------
     */
    protected static function booted()
    {
        // 🔹 Auto-generate a unique slug when creating
        static::creating(function ($category) {
            if (empty($category->slug)) {
                $baseSlug = Str::slug($category->name);
                $slug = $baseSlug;
                $i = 1;

                while (static::where('slug', $slug)->exists()) {
                    $slug = $baseSlug . '-' . $i++;
                }

                $category->slug = $slug;
            }
        });

        // 🔹 Update slug when name changes (optional but useful)
        static::updating(function ($category) {
            if (empty($category->slug)) {
                $baseSlug = Str::slug($category->name);
                $slug = $baseSlug;
                $i = 1;

                while (static::where('slug', $slug)
                    ->where('id', '!=', $category->id)
                    ->exists()) {
                    $slug = $baseSlug . '-' . $i++;
                }

                $category->slug = $slug;
            }
        });

        // 🔹 Cascade delete — ensures no orphan subcategories remain
        static::deleting(function ($category) {
            foreach ($category->children as $child) {
                $child->delete();
            }
        });
    }

    /**
     * ----------------------------------------------------
     * 🔗 Relationships
     * ----------------------------------------------------
     */

    // 🔹 Parent Category
    public function parent()
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    // 🔹 Child Categories
    public function children()
    {
        return $this->hasMany(Category::class, 'parent_id');
    }

    // 🔹 Related Products
    public function products()
    {
        return $this->hasMany(Product::class, 'category_id');
    }

    /**
     * ----------------------------------------------------
     * 🔍 Scopes
     * ----------------------------------------------------
     */

    // Top-level categories (no parent)
    public function scopeTopLevel($query)
    {
        return $query->whereNull('parent_id');
    }

    // Sub-categories (has parent)
    public function scopeSubCategories($query)
    {
        return $query->whereNotNull('parent_id');
    }

    /**
     * ----------------------------------------------------
     * 🧩 Accessors
     * ----------------------------------------------------
     */

    // Automatically capitalize category name when accessed
    public function getNameAttribute($value)
    {
        return ucfirst($value);
    }
}
