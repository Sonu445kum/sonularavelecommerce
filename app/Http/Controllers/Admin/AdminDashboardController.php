<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Models\Category;
use App\Models\Payment;
use App\Models\Notification;
use Carbon\Carbon;

class AdminDashboardController extends Controller
{
    /**
     * Display the admin dashboard with all key metrics, recent records, and chart data.
     */
    public function index()
    {
        // =========================
        // 📊 Summary Statistics
        // =========================
        $totalProducts = Product::count();
        $totalOrders = Order::count();
        $totalCategories = Category::count();
        $totalUsers = User::count();
        $totalRevenue = Payment::where('status', 'success')->sum('amount');
        $successfulPayments = Payment::where('status', 'success')->count();

        // =========================
        // 🧾 Recent Records (latest 5)
        // =========================
        $recentOrders = Order::with('user')->latest()->take(5)->get();
        $recentUsers = User::latest()->take(5)->get();
        $recentPayments = Payment::with('order.user')->latest()->take(5)->get();

        // Optional notifications
        $notifications = class_exists(Notification::class)
            ? Notification::latest()->take(5)->get()
            : collect();

        // =========================
        // 📈 Chart Data
        // =========================

        // 1️⃣ Orders chart: Last 7 days
        $ordersLast7Days = Order::selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->where('created_at', '>=', Carbon::now()->subDays(6))
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->keyBy('date');

        $ordersChartLabels = [];
        $ordersChartData = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i)->format('d M');
            $ordersChartLabels[] = $date;
            $count = $ordersLast7Days->has(Carbon::now()->subDays($i)->toDateString())
                ? $ordersLast7Days[Carbon::now()->subDays($i)->toDateString()]->count
                : 0;
            $ordersChartData[] = $count;
        }

        // 2️⃣ Revenue chart: Last 6 months
        $revenueChartLabels = [];
        $revenueChartData = [];
        for ($i = 5; $i >= 0; $i--) {
            $monthStart = Carbon::now()->subMonths($i)->startOfMonth();
            $monthEnd = Carbon::now()->subMonths($i)->endOfMonth();

            $revenueChartLabels[] = $monthStart->format('M Y');

            $total = Payment::where('status', 'success')
                ->whereBetween('created_at', [$monthStart, $monthEnd])
                ->sum('amount');

            $revenueChartData[] = $total;
        }

        // 3️⃣ Users growth chart: Last 6 months
        $usersChartLabels = $revenueChartLabels; // same as revenue months
        $usersChartData = [];
        for ($i = 5; $i >= 0; $i--) {
            $monthStart = Carbon::now()->subMonths($i)->startOfMonth();
            $monthEnd = Carbon::now()->subMonths($i)->endOfMonth();

            $count = User::whereBetween('created_at', [$monthStart, $monthEnd])->count();
            $usersChartData[] = $count;
        }

        // =========================
        // 📊 Revenue Distribution per Product
        // =========================
        $products = Product::with('orders')->get();
        $revenueDistributionLabels = [];
        $revenueDistributionData = [];

        foreach ($products as $product) {
            $revenueDistributionLabels[] = $product->name;
            // Total revenue per product
            $revenueDistributionData[] = $product->orders->sum('total');
        }

        // =========================
        // 🔄 Return view with all data
        // =========================
        return view('admin.dashboard', compact(
            'totalProducts',
            'totalOrders',
            'totalCategories',
            'totalUsers',
            'totalRevenue',
            'successfulPayments',
            'recentOrders',
            'recentUsers',
            'recentPayments',
            'notifications',
            'ordersChartLabels',
            'ordersChartData',
            'revenueChartLabels',
            'revenueChartData',
            'usersChartLabels',
            'usersChartData',
            'revenueDistributionLabels',
            'revenueDistributionData'
        ));
    }
}
