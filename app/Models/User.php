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
 * User Model
 *
 * Handles user authentication, roles, soft deletes,
 * email verification, and relationships with orders, reviews, wishlist, etc.
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
        'is_admin',     // For Admin Middleware
        'is_active',    // For account status management
    ];

    /**
     * The attributes that should be hidden for arrays or JSON responses.
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
     * ✅ Automatically hash password if not already hashed.
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

    public function notificationsCustom()
    {
        return $this->hasMany(Notification::class);
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
        return $query->where('is_blocked', false)->where('is_active', true);
    }

    public function scopeAdmins($query)
    {
        return $query->where('role', 'admin')->orWhere('is_admin', true);
    }

    public function scopeVendors($query)
    {
        return $query->where('role', 'vendor');
    }

    /* ============================================================
     |                        HELPER METHODS
     |============================================================ */

    /**
     * ✅ Role-based checks
     */
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

    /**
     * ✅ Status checks
     */
    public function isBlocked(): bool
    {
        return (bool) $this->is_blocked;
    }

    public function isActive(): bool
    {
        return (bool) $this->is_active;
    }

    /* ============================================================
     |                        EVENTS / HOOKS
     |============================================================ */

    protected static function booted()
    {
        static::deleting(function ($user) {
            // Clean up tokens on user delete (if table exists)
            try {
                if (method_exists($user, 'tokens') && Schema::hasTable('personal_access_tokens')) {
                    $user->tokens()->delete();
                }
            } catch (\Exception $e) {
                // Silently fail if table doesn't exist or token deletion fails
                // This allows user deletion to proceed even if tokens table is missing
            }
        });
    }
}
