<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Notification;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    /**
     * 📨 Show all notifications (paginated).
     */
    public function index()
    {
        $user = Auth::user();

        if (!$user) {
            return redirect()->route('login')->withErrors('Please log in to view notifications.');
        }

        $notifications = $user->notificationsCustom()->paginate(20);

        return view('notifications.index', compact('notifications'));
    }

    /**
     * 🔔 Fetch unread notifications (latest 5).
     */
    public function getUnread()
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json(['count' => 0, 'notifications' => []], 401);
        }

        $notifications = $user->notificationsCustom()
            ->where('is_read', false)
            ->latest()
            ->take(5)
            ->get();

        return response()->json([
            'count' => $notifications->count(),
            'notifications' => $notifications,
        ]);
    }

    /**
     * ✅ Mark a single notification as read.
     */
    public function markAsRead($id)
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $notification = Notification::where('id', $id)
            ->where('user_id', $user->id)
            ->first();

        if (!$notification) {
            return response()->json(['error' => 'Notification not found'], 404);
        }

        $notification->update([
            'is_read' => true,
            'updated_at' => now(),
        ]);

        return response()->json(['success' => true]);
    }

    /**
     * 🧹 Mark all notifications as read.
     */
    public function markAllAsRead()
    {
        $user = Auth::user();

        if (!$user) {
            return redirect()->route('login')->withErrors('Please log in first.');
        }

        $user->notificationsCustom()
            ->where('is_read', false)
            ->update([
                'is_read' => true,
                'updated_at' => now(),
            ]);

        return back()->with('success', 'All notifications marked as read.');
    }
}
