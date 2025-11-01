<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'user_id',
        'session_id',
        'subtotal',
    ];

    /**
     * ============================
     * 🔗 RELATIONSHIPS
     * ============================
     */

    // Each cart belongs to one user (nullable for guest users)
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // A cart can have many items
    public function items()
    {
        return $this->hasMany(CartItem::class);
    }

    /**
     * ============================
     * 🧮 HELPER METHODS
     * ============================
     */

    // Calculate subtotal dynamically from items
    public function calculateSubtotal()
    {
        return $this->items->sum(fn($item) => $item->price * $item->quantity);
    }

    /**
     * ============================
     * ⚙️ MODEL HOOKS
     * ============================
     */

    protected static function booted()
    {
        static::saving(function ($cart) {
            // Automatically recalculate subtotal when items are loaded
            if ($cart->relationLoaded('items')) {
                $cart->subtotal = $cart->calculateSubtotal();
            }
        });
    }
}
