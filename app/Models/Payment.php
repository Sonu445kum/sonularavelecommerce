<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    /**
     * ==============================
     * Table Configuration
     * ==============================
     */
    protected $table = 'payments'; // force correct table

    protected $fillable = [
        'order_id',
        'transaction_id',
        'status',
        'method',
        'amount',
        'meta',
    ];

    /**
     * ==============================
     * Casting Configuration
     * ==============================
     */
    protected $casts = [
        'meta' => 'array',  // JSON → array
        'amount' => 'decimal:2',
    ];

    /**
     * ==============================
     * Relationships
     * ==============================
     */

    // Payment belongs to Order
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * ==============================
     * Accessors & Helpers
     * ==============================
     */

    public function isSuccessful(): bool
    {
        return $this->status === 'success';
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isFailed(): bool
    {
        return $this->status === 'failed';
    }

    public function isRefunded(): bool
    {
        return $this->status === 'refunded';
    }

    /**
     * ==============================
     * Query Scopes
     * ==============================
     */

    public function scopeStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    public function scopeMethod($query, string $method)
    {
        return $query->where('method', $method);
    }

    public function scopeSuccessful($query)
    {
        return $query->where('status', 'success');
    }

    public function scopeFailed($query)
    {
        return $query->where('status', 'failed');
    }

    /**
     * ==============================
     * Utility Methods
     * ==============================
     */

    public function formattedAmount(): string
    {
        return number_format($this->amount, 2);
    }

    public function getTransactionReference(): string
    {
        return $this->transaction_id ?? 'N/A';
    }
}
