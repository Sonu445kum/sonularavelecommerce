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
use Illuminate\Support\Facades\DB;

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
        $totalRevenue = Payment::whereRaw('LOWER(status) = ?', ['success'])->sum('amount');
        $successfulPayments = Payment::whereRaw('LOWER(status) = ?', ['success'])->count();

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
        // Chart Data - monthly (last 6 months)
        // =========================
        $months = [];
        $ordersMonthly = [];
        $revenueMonthly = [];
        $paymentsMonthly = [];
        $usersMonthly = [];

        for ($i = 5; $i >= 0; $i--) {
            $start = Carbon::now()->subMonths($i)->startOfMonth();
            $end = Carbon::now()->subMonths($i)->endOfMonth();

            $months[] = $start->format('M Y');

            // Orders count in month
            $ordersMonthly[] = Order::whereBetween('created_at', [$start, $end])->count();

            // Users registered in month
            $usersMonthly[] = User::whereBetween('created_at', [$start, $end])->count();

            // Revenue (successful payments) in month
            $revenueMonthly[] = Payment::whereRaw('LOWER(status) = ?', ['success'])
                ->whereBetween('created_at', [$start, $end])
                ->sum('amount');

            // Payments (count) in month (all statuses)
            $paymentsMonthly[] = Payment::whereBetween('created_at', [$start, $end])->count();
        }

        // =========================
        // Revenue distribution (top products by revenue from order_items)
        // uses order_items.product_name and total_price
        // =========================
        $productRevenueRows = DB::table('order_items')
            ->select('product_name', DB::raw('SUM(total_price) as revenue'))
            ->groupBy('product_name')
            ->orderByDesc('revenue')
            ->limit(10)
            ->get();

        $revenueDistributionLabels = $productRevenueRows->pluck('product_name')->toArray();
        $revenueDistributionData = $productRevenueRows->pluck('revenue')->map(function($v){ return (float) $v; })->toArray();

        // =========================
        // Payment method distribution (successful payments)
        // =========================
        $methodRows = DB::table('payments')
            ->select('method', DB::raw('SUM(amount) as revenue'))
            ->whereRaw('LOWER(status) = ?', ['success'])
            ->groupBy('method')
            ->orderByDesc('revenue')
            ->get();

        $paymentMethodLabels = $methodRows->pluck('method')->toArray();
        $paymentMethodData = $methodRows->pluck('revenue')->map(function($v){ return (float) $v; })->toArray();

        // =========================
        // Simple growth metrics (compare last month vs previous month)
        // =========================
        $lastMonthStart = Carbon::now()->subMonth()->startOfMonth();
        $lastMonthEnd = Carbon::now()->subMonth()->endOfMonth();
        $prevMonthStart = Carbon::now()->subMonths(2)->startOfMonth();
        $prevMonthEnd = Carbon::now()->subMonths(2)->endOfMonth();

        $lastMonthRevenue = Payment::whereRaw('LOWER(status) = ?', ['success'])
            ->whereBetween('created_at', [$lastMonthStart, $lastMonthEnd])
            ->sum('amount');

        $prevMonthRevenue = Payment::whereRaw('LOWER(status) = ?', ['success'])
            ->whereBetween('created_at', [$prevMonthStart, $prevMonthEnd])
            ->sum('amount');

        $lastMonthOrders = Order::whereBetween('created_at', [$lastMonthStart, $lastMonthEnd])->count();
        $prevMonthOrders = Order::whereBetween('created_at', [$prevMonthStart, $prevMonthEnd])->count();

        $growthRateRevenue = $prevMonthRevenue > 0 ? round((($lastMonthRevenue - $prevMonthRevenue) / $prevMonthRevenue) * 100, 1) : null;
        $growthRateOrders = $prevMonthOrders > 0 ? round((($lastMonthOrders - $prevMonthOrders) / $prevMonthOrders) * 100, 1) : null;

        $growthRateData = [
            'revenue_last_month' => (float) $lastMonthRevenue,
            'revenue_prev_month' => (float) $prevMonthRevenue,
            'revenue_growth_percent' => $growthRateRevenue,
            'orders_last_month' => (int) $lastMonthOrders,
            'orders_prev_month' => (int) $prevMonthOrders,
            'orders_growth_percent' => $growthRateOrders,
        ];

        // =========================
        // Compact & return
        // =========================
        return view('admin.dashboard', compact(
            'totalProducts','totalOrders','totalCategories','totalUsers','totalRevenue','successfulPayments',
            'recentOrders','recentUsers','recentPayments','notifications',
            // monthly series
            'months','ordersMonthly','revenueMonthly','paymentsMonthly','usersMonthly',
            // distributions
            'revenueDistributionLabels','revenueDistributionData',
            'paymentMethodLabels','paymentMethodData',
            // growth
            'growthRateData'
        ));
    }
}
