<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CartItem extends Model
{
    use HasFactory;

    /**
     * ====================================
     * 💾 MASS ASSIGNABLE FIELDS
     * ====================================
     */
    protected $fillable = [
        'cart_id',
        'product_id',
        'product_variant_id',
        'quantity',
        'price',
        'total',
    ];

    /**
     * ====================================
     * 🔗 RELATIONSHIPS
     * ====================================
     */

    // Each cart item belongs to a cart
    public function cart()
    {
        return $this->belongsTo(Cart::class);
    }

    // Each cart item belongs to a product
    public function product()
{
    return $this->belongsTo(Product::class, 'product_id');
}

    // Each cart item may belong to a variant (optional)
    public function productVariant()
    {
        return $this->belongsTo(ProductVariant::class);
    }

    /**
     * ====================================
     * ⚙️ MODEL HOOKS
     * ====================================
     * Automatically calculate total (quantity * price)
     * every time the item is created or updated.
     */
    protected static function booted()
    {
        static::saving(function ($item) {
            $item->total = $item->quantity * $item->price;
        });
    }

    /**
     * ====================================
     * 🧮 ACCESSORS
     * ====================================
     * Get total dynamically (in case 'total' field not saved)
     */
    public function getTotalAttribute($value)
    {
        return $value ?? ($this->price * $this->quantity);
    }
}
