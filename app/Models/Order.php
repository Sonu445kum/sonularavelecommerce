<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'uuid',
        'order_number',
        'user_id',
        'address_id',
        'shipping_address', // ✅ Stored as JSON
        'subtotal',
        'shipping',
        'tax',
        'discount',
        'total',
        'status',
        'notes',
        'meta',
    ];

    protected $casts = [
        'meta'              => 'array',
        'shipping_address'  => 'array', 
        'subtotal'          => 'decimal:2',
        'shipping'          => 'decimal:2',
        'tax'               => 'decimal:2',
        'discount'          => 'decimal:2',
        'total'             => 'decimal:2',
    ];

    protected static function booted()
    {
        parent::booted();

        static::creating(function ($order) {
            if (empty($order->uuid)) {
                $order->uuid = (string) Str::uuid();
            }
            if (empty($order->order_number)) {
                $order->order_number = 'ORD-' . strtoupper(uniqid());
            }
            if (empty($order->total)) {
                $order->total = ($order->subtotal ?? 0)
                              + ($order->shipping ?? 0)
                              + ($order->tax ?? 0)
                              - ($order->discount ?? 0);
            }
            if (empty($order->status)) {
                $order->status = 'Pending';
            }
        });
    }

    // --------------------------
    // Relationships
    // --------------------------

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function address()
    {
        // Returns Address model if address_id exists
        return $this->belongsTo(\App\Models\Address::class, 'address_id');
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class, 'order_id');
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function latestPayment()
    {
        return $this->hasOne(Payment::class)->latestOfMany();
    }

    // --------------------------
    // Helper Methods
    // --------------------------

    public function getFormattedTotalAttribute(): string
    {
        return number_format($this->total, 2);
    }

    public function getComputedTotalAttribute(): float
    {
        return (float) ($this->subtotal + $this->shipping + $this->tax - $this->discount);
    }

    public function isDelivered(): bool
    {
        return strtolower($this->status) === 'delivered';
    }

    public function isPaid(): bool
    {
        return $this->payments()->where('status', 'success')->exists();
    }

    // --------------------------
    // Query Scopes
    // --------------------------
    public function scopeOfUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeMonthly($query)
    {
        return $query->whereMonth('created_at', now()->month);
    }

    public function scopeRevenue($query)
    {
        return $query->where('status', 'delivered')->sum('total');
    }

    // --------------------------
    // Get shipping info for blade
    // --------------------------
    public function getShippingInfoAttribute()
    {
        // If address_id exists, return related Address
        if ($this->address) {
            return $this->address;
        }

        // Otherwise, return array from checkout as object
        return $this->shipping_address ? (object) $this->shipping_address : null;
    }
}
