<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Order extends Model
{
    use HasFactory;

    /**
     * -------------------------------------------------
     * Mass assignable fields
     * -------------------------------------------------
     */
    protected $fillable = [
        'uuid',
        'order_number',
        'user_id',
        'address_id',
        'subtotal',
        'shipping',
        'tax',
        'discount',
        'total',
        'status',
        'notes',
        'meta',
    ];

    /**
     * -------------------------------------------------
     * Cast attributes to native types
     * -------------------------------------------------
     */
    protected $casts = [
        'meta'      => 'array',
        'subtotal'  => 'decimal:2',
        'shipping'  => 'decimal:2',
        'tax'       => 'decimal:2',
        'discount'  => 'decimal:2',
        'total'     => 'decimal:2',
    ];

    /**
     * -------------------------------------------------
     * Booted: Auto-generate UUID, Order Number & Total
     * -------------------------------------------------
     */
    protected static function booted()
    {
        parent::booted();

        static::creating(function ($order) {
            // 🔹 Auto-generate UUID if missing
            if (empty($order->uuid)) {
                $order->uuid = (string) Str::uuid();
            }

            // 🔹 Auto-generate unique order number
            if (empty($order->order_number)) {
                $order->order_number = 'ORD-' . strtoupper(uniqid());
            }

            // 🔹 Auto-calculate total if not provided
            if (empty($order->total)) {
                $order->total = ($order->subtotal ?? 0)
                              + ($order->shipping ?? 0)
                              + ($order->tax ?? 0)
                              - ($order->discount ?? 0);
            }

            // 🔹 Default status
            if (empty($order->status)) {
                $order->status = 'Pending';
            }
        });
    }

    /**
     * -------------------------------------------------
     * Relationships
     * -------------------------------------------------
     */

    // 🔹 Each order belongs to one user
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // 🔹 Each order belongs to one address (shipping/billing)
    public function address()
    {
        return $this->belongsTo(Address::class);
    }

    // 🔹 Each order has many order items
    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    // 🔹 Each order can have multiple payments
    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    // 🔹 Fetch only the latest successful payment
    public function latestPayment()
    {
        return $this->hasOne(Payment::class)->latestOfMany();
    }

    /**
     * -------------------------------------------------
     * Accessors / Helper Methods
     * -------------------------------------------------
     */

    // 🔹 Formatted total (2 decimal places)
    public function getFormattedTotalAttribute(): string
    {
        return number_format($this->total, 2);
    }

    // 🔹 Dynamically compute total (useful for recalculation)
    public function getComputedTotalAttribute(): float
    {
        return (float) ($this->subtotal + $this->shipping + $this->tax - $this->discount);
    }

    // 🔹 Check if order is delivered
    public function isDelivered(): bool
    {
        return strtolower($this->status) === 'delivered';
    }

    // 🔹 Check if payment success exists
    public function isPaid(): bool
    {
        return $this->payments()->where('status', 'success')->exists();
    }

    /**
     * -------------------------------------------------
     * Query Scopes (for dashboard filters)
     * -------------------------------------------------
     */

    // 🔹 Filter orders by user
    public function scopeOfUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    // 🔹 Filter by status
    public function scopeStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    // 🔹 Current month orders
    public function scopeMonthly($query)
    {
        return $query->whereMonth('created_at', now()->month);
    }

    // 🔹 Calculate total revenue
    public function scopeRevenue($query)
    {
        return $query->where('status', 'delivered')->sum('total');
    }
}
