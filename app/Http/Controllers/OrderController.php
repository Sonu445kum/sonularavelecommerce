<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Order;

class OrderController extends Controller
{
    /**
     * ==========================================
     * Display All Orders (User / Admin)
     * ==========================================
     */
    public function index()
    {
        $user = Auth::user();

        if (!$user) {
            return redirect()->route('login')->with('error', 'Please login to view your orders.');
        }

        // 🔹 Admin can view all orders
        $orders = $user->is_admin
            ? Order::with(['user', 'items.product'])
                ->latest()
                ->paginate(20)
            : $user->orders()
                ->with(['items.product'])
                ->latest()
                ->paginate(20);

        // 🔹 Handle empty order list
        if ($orders->isEmpty()) {
            session()->flash('info', 'You have not placed any orders yet.');
        }

        return view('orders.index', compact('orders'));
    }

    /**
     * ==========================================
     * Display Single Order Details
     * ==========================================
     */
    public function show($id)
    {
        $order = Order::with([
            'user',
            'items.product',
            'address',
            'payments' => function ($q) {
                $q->latest();
            }
        ])->findOrFail($id);

        // 🔹 Access Control
        if (Auth::id() !== $order->user_id && !Auth::user()?->is_admin) {
            abort(403, 'Unauthorized access to order details.');
        }

        // 🔹 Format Order Data (Optional - For Safety)
        $order->formatted_total = number_format($order->total, 2);
        $order->item_count = $order->items->sum('quantity');

        return view('orders.show', compact('order'));
    }

    /**
     * ==========================================
     * Cancel Order (Optional Feature)
     * ==========================================
     */
    public function cancel($id)
    {
        $order = Order::findOrFail($id);

        if (Auth::id() !== $order->user_id && !Auth::user()?->is_admin) {
            abort(403, 'Unauthorized to cancel this order.');
        }

        if (in_array($order->status, ['shipped', 'delivered'])) {
            return back()->with('error', 'You cannot cancel an order that is already shipped or delivered.');
        }

        $order->status = 'Cancelled';
        $order->save();

        return redirect()->route('orders.index')->with('success', 'Order has been cancelled successfully.');
    }
}
