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
    public function index()
    {
        // =========================
        // Summary Stats
        // =========================
        $totalProducts = Product::count();
        $totalOrders = Order::count();
        $totalCategories = Category::count();
        $totalUsers = User::count();
        $totalRevenue = Payment::where('status', 'success')->sum('amount');
        $successfulPayments = Payment::where('status', 'success')->count();

        // =========================
        // Recent Records (5 latest)
        // =========================
        $recentOrders = Order::with('user')->latest()->take(5)->get();
        $recentUsers = User::latest()->take(5)->get();
        $recentPayments = Payment::with('order.user')->latest()->take(5)->get();

        // Notifications
        $notifications = class_exists(Notification::class)
            ? Notification::latest()->take(5)->get()
            : collect();

        // =========================
        // Chart Data
        // =========================

        // Orders last 7 days
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
            $ordersChartData[] = $ordersLast7Days->has(Carbon::now()->subDays($i)->toDateString())
                ? $ordersLast7Days[Carbon::now()->subDays($i)->toDateString()]->count
                : 0;
        }

        // Revenue last 6 months
        $revenueChartLabels = [];
        $revenueChartData = [];
        for ($i = 5; $i >= 0; $i--) {
            $monthStart = Carbon::now()->subMonths($i)->startOfMonth();
            $monthEnd = Carbon::now()->subMonths($i)->endOfMonth();
            $revenueChartLabels[] = $monthStart->format('M Y');
            $revenueChartData[] = Payment::where('status', 'success')
                ->whereBetween('created_at', [$monthStart, $monthEnd])
                ->sum('amount');
        }

        // Users growth chart (last 6 months)
        $usersChartLabels = $revenueChartLabels;
        $usersChartData = [];
        for ($i = 5; $i >= 0; $i--) {
            $monthStart = Carbon::now()->subMonths($i)->startOfMonth();
            $monthEnd = Carbon::now()->subMonths($i)->endOfMonth();
            $usersChartData[] = User::whereBetween('created_at', [$monthStart, $monthEnd])->count();
        }

        // Revenue Distribution per Product
        $products = Product::with('orders')->get();
        $revenueDistributionLabels = [];
        $revenueDistributionData = [];
        foreach ($products as $product) {
            $revenueDistributionLabels[] = $product->name;
            $revenueDistributionData[] = $product->orders->sum('total');
        }

        // Dashboard line chart
        $productsChartData = [];
        $paymentsChartData = [];
        $dashboardLabels = $revenueChartLabels;
        for ($i = 5; $i >= 0; $i--) {
            $monthStart = Carbon::now()->subMonths($i)->startOfMonth();
            $monthEnd = Carbon::now()->subMonths($i)->endOfMonth();
            $productsChartData[] = Order::whereBetween('created_at', [$monthStart, $monthEnd])->sum('quantity');
            $paymentsChartData[] = Payment::whereBetween('created_at', [$monthStart, $monthEnd])->count();
        }

        // Growth Rate (Last month)
        $growthRateData = [
            User::where('created_at', '>=', Carbon::now()->subMonth())->count(),
            Order::where('created_at', '>=', Carbon::now()->subMonth())->count(),
            Payment::where('status', 'success')->where('created_at', '>=', Carbon::now()->subMonth())->sum('amount')
        ];

        return view('admin.dashboard', compact(
            'totalProducts','totalOrders','totalCategories','totalUsers','totalRevenue','successfulPayments',
            'recentOrders','recentUsers','recentPayments','notifications',
            'ordersChartLabels','ordersChartData',
            'revenueChartLabels','revenueChartData',
            'usersChartLabels','usersChartData',
            'productsChartData','paymentsChartData','dashboardLabels',
            'revenueDistributionLabels','revenueDistributionData','growthRateData'
        ));
    }
}
