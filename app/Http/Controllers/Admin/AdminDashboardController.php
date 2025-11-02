<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Models\Category;
use App\Models\Payment;
use App\Models\Wishlist;

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
        $totalRevenue = Order::where('status', 'completed')->sum('total_amount');
        $pendingPayments = Payment::where('status', 'pending')->count();
        $wishlistCount = Wishlist::count();

        // 🧾 Recent Orders (latest 5)
        $recentOrders = Order::with('user')->latest()->take(5)->get();

        // 👤 Recent Users
        $recentUsers = User::latest()->take(5)->get();

        // 💳 Recent Payments
        $recentPayments = Payment::latest()->take(5)->get();

        return view('admin.dashboard', compact(
            'totalOrders',
            'totalUsers',
            'totalProducts',
            'totalCategories',
            'totalRevenue',
            'pendingPayments',
            'wishlistCount',
            'recentOrders',
            'recentUsers',
            'recentPayments'
        ));
    }
}
