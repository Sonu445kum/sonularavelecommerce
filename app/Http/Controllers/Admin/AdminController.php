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
     */
    public function dashboard()
    {
        // -------- BASIC STATS --------
        $totalProducts = Product::count();
        $totalOrders = Order::count();
        $totalCategories = Category::count();
        $totalUsers = User::count();

        $totalRevenue = Order::whereIn('status', ['completed', 'delivered'])->sum('total');
        $successfulPayments = Payment::where('status', 'success')->count();
        $wishlistCount = Wishlist::count();

        $recentOrders = Order::with('user')->latest()->take(10)->get();
        $recentUsers = User::latest()->take(10)->get();
        $recentPayments = Payment::with(['order.user'])->latest()->take(10)->get();

        // -------- NOTIFICATIONS --------
        $notifications = Notification::where('user_id', auth()->id())
            ->latest()
            ->take(10)
            ->get();

        $unreadCount = Notification::where('user_id', auth()->id())
            ->where('is_read', false)
            ->count();

        // -------- MONTHLY CHART DATA --------
        $months = collect(range(1, 12))->map(fn($m) => date("M", mktime(0, 0, 0, $m, 1)));

        $ordersMonthly = [];
        $revenueMonthly = [];
        $paymentsMonthly = [];
        $usersMonthly = [];

        foreach (range(1, 12) as $month) {
            $ordersMonthly[] = Order::whereMonth('created_at', $month)->count();
            $revenueMonthly[] = Order::whereMonth('created_at', $month)->sum('total');
            $paymentsMonthly[] = Payment::whereMonth('created_at', $month)->count();
            $usersMonthly[] = User::whereMonth('created_at', $month)->count();
        }

        // -------- REVENUE DISTRIBUTION CHART --------
        $revenueDistributionLabels = ['Product A', 'Product B', 'Product C', 'Product D', 'Product E'];
        $revenueDistributionData = [
            rand(5000, 15000),
            rand(3000, 10000),
            rand(2000, 8000),
            rand(4000, 12000),
            rand(1000, 6000),
        ];

        // -------- PAYMENT METHOD CHART --------
        $paymentMethodLabels = ['COD', 'Razorpay', 'Stripe', 'PayPal'];
        $paymentMethodData = Payment::getPaymentMethodCounts(['cod', 'razorpay', 'stripe', 'paypal']);
        $paymentMethodData = array_values($paymentMethodData); // convert to numeric array for chart

        // -------- GROWTH RATE CHART --------
        $usersPrev  = User::whereMonth('created_at', now()->subMonth()->month)->count();
        $usersNow   = User::whereMonth('created_at', now()->month)->count();

        $ordersPrev = Order::whereMonth('created_at', now()->subMonth()->month)->count();
        $ordersNow  = Order::whereMonth('created_at', now()->month)->count();

        $revenuePrev = Order::whereMonth('created_at', now()->subMonth()->month)->sum('total');
        $revenueNow  = Order::whereMonth('created_at', now()->month)->sum('total');

        $growthRateData = [
            $this->growthCalc($usersPrev, $usersNow),
            $this->growthCalc($ordersPrev, $ordersNow),
            $this->growthCalc($revenuePrev, $revenueNow),
        ];

        return view('admin.dashboard', compact(
            'totalProducts',
            'totalOrders',
            'totalCategories',
            'totalUsers',
            'totalRevenue',
            'successfulPayments',
            'wishlistCount',
            'recentOrders',
            'recentUsers',
            'recentPayments',
            'notifications',
            'unreadCount',

            // chart data
            'months',
            'ordersMonthly',
            'revenueMonthly',
            'paymentsMonthly',
            'usersMonthly',

            'revenueDistributionLabels',
            'revenueDistributionData',

            'paymentMethodLabels',
            'paymentMethodData',

            'growthRateData',
        ));
    }

    /**
     * Calculate growth percentage
     */
    private function growthCalc($prev, $now)
    {
        if ($prev == 0 && $now == 0) return 0;
        if ($prev == 0) return 100;
        return round((($now - $prev) / $prev) * 100, 2);
    }

    /**
     * ------------------------------------------
     * 📨 Handle New Order Notification
     * ------------------------------------------
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

        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
        ];

        if ($request->hasFile('profile_image')) {
            $rules['profile_image'] = 'required|image|mimes:jpeg,jpg,png|max:2048';
        }

        $validated = $request->validate($rules);

        if ($request->hasFile('profile_image')) {
            $profileImage = $request->file('profile_image');
            if ($profileImage->isValid()) {
                if ($user->profile_image && Storage::disk('public')->exists($user->profile_image)) {
                    Storage::disk('public')->delete($user->profile_image);
                }

                $path = $profileImage->store('profile_images', 'public');
                $user->profile_image = $path;
            }
        }

        $user->name = $request->name;
        $user->email = $request->email;
        $user->save();

        return redirect()->route('admin.profile.index')->with('success', 'Profile updated successfully!');
    }
}
