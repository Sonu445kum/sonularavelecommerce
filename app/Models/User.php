<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Schema;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Str;

/**
 * Class User
 *
 * Represents an application user with authentication, roles, 
 * soft deletes, notifications, and relationships to orders, reviews, etc.
 */
class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'role',
        'is_blocked',
        'is_admin',
        'is_active',
    ];

    /**
     * The attributes that should be hidden for arrays or JSON.
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'is_blocked' => 'boolean',
        'is_admin' => 'boolean',
        'is_active' => 'boolean',
    ];

    /* ============================================================
     |                        MUTATORS
     |============================================================ */

    /**
     * ✅ Automatically hash the password if it's not already hashed.
     */
    public function setPasswordAttribute($value)
    {
        if (!empty($value)) {
            $this->attributes['password'] = Str::startsWith($value, '$2y$')
                ? $value
                : bcrypt($value);
        }
    }

    /* ============================================================
     |                        RELATIONSHIPS
     |============================================================ */

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function cart()
    {
        return $this->hasOne(Cart::class);
    }

    public function cartItems()
    {
        return $this->hasMany(CartItem::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    /**
     * ✅ Custom relationship for in-app notifications.
     */
    public function notificationsCustom()
    {
        return $this->hasMany(Notification::class, 'user_id')
                    ->orderByDesc('created_at');
    }

    public function coupons()
    {
        return $this->belongsToMany(Coupon::class, 'user_coupons')->withTimestamps();
    }

    public function wishlist()
    {
        return $this->hasMany(Wishlist::class, 'user_id');
    }

    /* ============================================================
     |                        SCOPES
     |============================================================ */

    public function scopeActive($query)
    {
        return $query->where('is_blocked', false)
                     ->where('is_active', true);
    }

    public function scopeAdmins($query)
    {
        return $query->where('role', 'admin')
                     ->orWhere('is_admin', true);
    }

    public function scopeVendors($query)
    {
        return $query->where('role', 'vendor');
    }

    /* ============================================================
     |                        ROLE & STATUS HELPERS
     |============================================================ */

    public function isAdmin(): bool
    {
        return $this->role === 'admin' || (bool) $this->is_admin;
    }

    public function isVendor(): bool
    {
        return $this->role === 'vendor';
    }

    public function isCustomer(): bool
    {
        return $this->role === 'customer';
    }

    public function isBlocked(): bool
    {
        return (bool) $this->is_blocked;
    }

    public function isActive(): bool
    {
        return (bool) $this->is_active;
    }

    /* ============================================================
     |                        MODEL EVENTS
     |============================================================ */

    protected static function booted()
    {
        static::deleting(function ($user) {
            try {
                // ✅ Clean up tokens on user delete
                if (method_exists($user, 'tokens') && Schema::hasTable('personal_access_tokens')) {
                    $user->tokens()->delete();
                }
            } catch (\Exception $e) {
                // Fail silently if the table or tokens don’t exist
            }
        });
    }
}
