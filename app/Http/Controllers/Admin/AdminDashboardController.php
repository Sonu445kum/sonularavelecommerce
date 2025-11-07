<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Models\Category;
use App\Models\Payment;
use App\Models\Notification;

class AdminDashboardController extends Controller
{
    /**
     * Display admin dashboard summary with all key metrics.
     */
    public function index()
    {
        // 📊 Summary Statistics
        $totalOrders = Order::count();
        $totalUsers = User::count();
        $totalProducts = Product::count();
        $totalCategories = Category::count();

        // 💰 Correct Total Revenue from Payments table (only successful)
        $totalRevenue = Payment::where('status', 'success')->sum('amount');

        // ✅ Count of successful payments
        $successfulPayments = Payment::where('status', 'success')->count();

        // Debug check (optional - comment after testing)
        dd([
            'totalRevenue' => $totalRevenue,
            'successfulPayments' => $successfulPayments,
            'all_status' => Payment::select('id','status','amount')->get(),
        ]);

        // 🧾 Recent Orders
        $recentOrders = Order::with('user')->latest()->take(5)->get();

        // 👤 Recent Users
        $recentUsers = User::latest()->take(5)->get();

        // 💳 Recent Payments
        $recentPayments = Payment::with(['order.user'])->latest()->take(5)->get();

        // 🔔 Notifications (optional)
        $notifications = class_exists(Notification::class)
            ? Notification::latest()->take(5)->get()
            : collect();

        return view('admin.dashboard', compact(
            'totalOrders',
            'totalUsers',
            'totalProducts',
            'totalCategories',
            'totalRevenue',
            'successfulPayments',
            'recentOrders',
            'recentUsers',
            'recentPayments',
            'notifications'
        ));
    }
}
