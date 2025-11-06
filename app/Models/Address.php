<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'label',
        'name',
        'phone',
        'address_line1',
        'address_line2',
        'city',
        'state',
        'postal_code',
        'country',
        'is_default',
    ];

    protected $casts = [
        'is_default' => 'boolean',
    ];

    /**
     * User who owns this address
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Orders linked with this address
     */
    public function orders()
    {
        return $this->hasMany(Order::class, 'address_id');
    }

    /**
     * Scope for default address
     */
    public function scopeDefault($query)
    {
        return $query->where('is_default', true);
    }

    /**
     * Return full address string
     */
    public function getFullAddressAttribute(): string
    {
        $address = "{$this->address_line1}";
        if (!empty($this->address_line2)) {
            $address .= ", {$this->address_line2}";
        }
        $address .= ", {$this->city}, {$this->state} - {$this->postal_code}, {$this->country}";
        return $address;
    }
}
