<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Mail;
use App\Models\Product;
use App\Models\Order;
use App\Models\Category;
use App\Models\User;
use App\Models\Payment;
use App\Models\Wishlist;
use App\Models\Notification;
use App\Mail\OrderPlacedMail;

class AdminController extends Controller
{
    /**
     * ------------------------------------------
     * 🏠 Admin Dashboard
     * ------------------------------------------
     * Show the main admin dashboard page with notifications
     */
    public function dashboard()
    {
        // 🔢 Stats
        // ==================== Add this ====================
        $totalProducts = Product::count();
        $totalOrders = Order::count();
        $totalCategories = Category::count();
        $totalUsers = User::count();

        // 💰 Total revenue (completed/delivered)
        $totalRevenue = Order::whereIn('status', ['completed', 'delivered'])->sum('total');

        // ⏳ Pending payments
        $pendingPayments = Payment::where('status', 'pending')->count();

        // 💖 Total wishlists
        $wishlistCount = Wishlist::count();

        // 🧾 Recent orders
        $recentOrders = Order::with('user')->latest()->take(10)->get();

        // 👥 Recent users
        $recentUsers = User::latest()->take(10)->get();

        // 💳 Recent payments
        $recentPayments = Payment::with(['order.user'])->latest()->take(10)->get();

        // 🔔 Admin Notifications
        $notifications = Notification::where('user_id', auth()->id())
            ->latest()
            ->take(10)
            ->get();

        $unreadCount = Notification::where('user_id', auth()->id())
            ->where('is_read', false)
            ->count();

        return view('admin.dashboard', compact(
            'totalProducts',
            'totalOrders',
            'totalCategories',
            'totalUsers',
            'totalRevenue',
            'pendingPayments',
            'wishlistCount',
            'recentOrders',
            'recentUsers',
            'recentPayments',
            'notifications',
            'unreadCount',
        ));
    }

    /**
     * ------------------------------------------
     * 📨 Handle New Order Notification
     * ------------------------------------------
     * When a new order is placed, create admin notification + email
     */
    public static function notifyNewOrder(Order $order)
    {
        // 🧾 Create admin notification
        Notification::sendToAdmin(
            '🛒 New Order Received',
            "Order #{$order->id} placed by {$order->user->name} (Total ₹{$order->total})",
            [
                'order_id' => $order->id,
                'amount' => $order->total,
                'user' => $order->user->name,
            ]
        );

        // 📧 Send confirmation email to user
        Mail::to($order->user->email)->send(new OrderPlacedMail($order));
    }

    /**
     * ------------------------------------------
     * 👤 Show Admin Profile
     * ------------------------------------------
     */
    public function profile()
    {
        $user = auth()->user();
        return view('admin.profile', compact('user'));
    }

    /**
     * ------------------------------------------
     * ✏️ Edit Admin Profile
     * ------------------------------------------
     */
    public function editProfile()
    {
        $user = auth()->user();
        return view('admin.edit-profile', compact('user'));
    }

    /**
     * ------------------------------------------
     * 💾 Update Admin Profile
     * ------------------------------------------
     */
    public function updateProfile(Request $request)
    {
        $user = auth()->user();

        // ✅ Validate
        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
        ];

        if ($request->hasFile('profile_image')) {
            $file = $request->file('profile_image');
            if ($file && $file->isValid()) {
                $rules['profile_image'] = 'required|image|mimes:jpeg,jpg,png|max:2048';
            }
        }

        $validated = $request->validate($rules);

        // 📸 Handle Profile Image
        if ($request->hasFile('profile_image')) {
            $profileImage = $request->file('profile_image');
            if ($profileImage && $profileImage->isValid()) {
                if ($user->profile_image && Storage::disk('public')->exists($user->profile_image)) {
                    Storage::disk('public')->delete($user->profile_image);
                }

                $path = $profileImage->store('profile_images', 'public');
                $user->profile_image = $path;
            }
        }

        // 📝 Update user info
        $user->name = $request->name;
        $user->email = $request->email;
        $user->save();

        return redirect()->route('admin.profile.index')->with('success', 'Profile updated successfully!');
    }
}
