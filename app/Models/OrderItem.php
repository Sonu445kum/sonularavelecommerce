<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    use HasFactory;

    /**
     * =========================================================
     * ✅ Mass Assignable Attributes
     * =========================================================
     */
    protected $fillable = [
        'order_id',
        'product_id',
        'product_variant_id',
        'product_name',
        'product_sku',
        'product_image',
        'quantity',
        'unit_price',
        'total_price',
        'meta',
    ];

    /**
     * =========================================================
     * ✅ Attribute Casting
     * =========================================================
     */
    protected $casts = [
        'meta' => 'array',
        'unit_price' => 'decimal:2',
        'total_price' => 'decimal:2',
    ];

    /**
     * =========================================================
     * ✅ Relationships
     * =========================================================
     */

    // 🔹 Each OrderItem belongs to one Order
    public function order()
    {
        return $this->belongsTo(Order::class);
    }
    
    // 🔹 Each OrderItem belongs to one Product
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    // 🔹 Optional variant relationship
    public function variant()
    {
        return $this->belongsTo(ProductVariant::class, 'product_variant_id');
    }

    /**
     * =========================================================
     * ✅ Accessors & Helper Methods
     * =========================================================
     */

    // 🔹 Dynamically compute subtotal
    public function getSubtotalAttribute(): float
    {
        return (float) ($this->quantity * $this->unit_price);
    }

    // 🔹 Formatted total price (for UI)
    public function getFormattedTotalAttribute(): string
    {
        return number_format($this->total_price, 2);
    }

    /**
     * =========================================================
     * ✅ Booted Events — Auto Data Handling
     * =========================================================
     */
    protected static function booted()
    {
        static::creating(function ($item) {
            $product = $item->product ?? Product::find($item->product_id);

            if ($product) {
                // 🧩 Auto-fill product details if not set
                $item->product_name  = $item->product_name  ?? $product->name;
                $item->product_sku   = $item->product_sku   ?? $product->sku ?? 'N/A';
                $item->product_image = $item->product_image ?? $product->image ?? 'images/no-image.png';
                $item->unit_price    = $item->unit_price    ?? $product->price ?? 0;
            }

            // 🧮 Always calculate total_price
            $item->total_price = ($item->unit_price ?? 0) * ($item->quantity ?? 1);
        });

        static::updating(function ($item) {
            $item->total_price = ($item->unit_price ?? 0) * ($item->quantity ?? 1);
        });
    }

    /**
     * =========================================================
     * ✅ Query Scopes
     * =========================================================
     */

    public function scopeOfOrder($query, int $orderId)
    {
        return $query->where('order_id', $orderId);
    }

    public function scopeOfProduct($query, int $productId)
    {
        return $query->where('product_id', $productId);
    }

    /**
     * =========================================================
     * ✅ Utility Methods
     * =========================================================
     */

    // 🔹 Check if this item has a variant
    public function hasVariant(): bool
    {
        return !is_null($this->product_variant_id);
    }

    // 🔹 Recalculate and update total price manually
    public function calculateTotal(): float
    {
        $this->total_price = (float) ($this->quantity * $this->unit_price);
        return $this->total_price;
    }
}
