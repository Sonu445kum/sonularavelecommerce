<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Mail;
use App\Mail\GenericNotificationMail; // ✅ Custom mailable for email notifications

class Notification extends Model
{
    use HasFactory;

    /**
     * Table name
     */
    protected $table = 'notifications';

    /**
     * Mass assignable attributes
     */
    protected $fillable = [
        'user_id',
        'type',
        'title',
        'message',
        'data',
        'is_read',
        'read_at',
    ];

    /**
     * Attribute casting
     */
    protected $casts = [
        'data' => 'array',
        'is_read' => 'boolean',
        'read_at' => 'datetime',
    ];

    /**
     * Relationship → each notification belongs to a user
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope for unread notifications
     */
    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }

    /**
     * Scope for read notifications
     */
    public function scopeRead($query)
    {
        return $query->where('is_read', true);
    }

    /**
     * Mark the notification as read
     */
    public function markAsRead()
    {
        $this->update([
            'is_read' => true,
            'read_at' => now(),
        ]);
    }

    /**
     * 🔹 Send notification to a specific user (and optionally email)
     */
    public static function sendToUser($userId, $title, $message, $data = [], $sendEmail = true)
    {
        $notification = self::create([
            'user_id' => $userId,
            'type' => 'UserAlert',
            'title' => $title,
            'message' => $message,
            'data' => $data,
            'is_read' => false,
        ]);

        if ($sendEmail) {
            $user = \App\Models\User::find($userId);

            if ($user && $user->email) {
                try {
                    Mail::to($user->email)->send(new GenericNotificationMail($title, $message));
                } catch (\Exception $e) {
                    \Log::error('❌ Notification Email Failed: ' . $e->getMessage());
                }
            }
        }

        return $notification;
    }

    /**
     * 🔹 Send notification to Admin (default admin = first user with is_admin = true)
     */
    public static function sendToAdmin($title, $message, $data = [])
    {
        $admin = \App\Models\User::where('is_admin', true)->first();

        return self::sendToUser(
            $admin?->id ?? 1,
            $title,
            $message,
            $data,
            true
        );
    }

    /**
     * 🔹 Shortcut: Notify both User & Admin when an order is placed
     */
    public static function orderPlaced($userId, $orderId)
    {
        $userMsg = "Your order #{$orderId} has been placed successfully!";
        $adminMsg = "New Order #{$orderId} received from User ID: {$userId}.";

        // User notification
        self::sendToUser($userId, 'Order Placed', $userMsg, ['order_id' => $orderId]);

        // Admin notification
        self::sendToAdmin('New Order Received', $adminMsg, ['order_id' => $orderId]);
    }

    /**
     * 🔹 Shortcut: Notify when payment succeeds
     */
    public static function paymentSuccess($userId, $orderId, $amount)
    {
        $msg = "Payment of ₹{$amount} for Order #{$orderId} was successful.";
        self::sendToUser($userId, 'Payment Successful', $msg, ['order_id' => $orderId]);
        self::sendToAdmin('Payment Received', "User ID {$userId} paid ₹{$amount} for Order #{$orderId}", ['order_id' => $orderId]);
    }

    /**
     * 🔹 Shortcut: Notify delivery update
     */
    public static function orderDelivered($userId, $orderId)
    {
        $msg = "Your order #{$orderId} has been delivered. We hope you enjoy your purchase!";
        self::sendToUser($userId, 'Order Delivered', $msg, ['order_id' => $orderId]);
    }
}
