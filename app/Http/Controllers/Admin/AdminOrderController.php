<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;

class AdminOrderController extends Controller
{
    /**
     * Display a listing of orders for admin.
     */
    public function adminIndex()
    {
        $orders = Order::with(['user', 'latestPayment'])->latest()->paginate(20);
        return view('admin.orders.index', compact('orders'));
    }

    /**
     * Display a listing of orders.
     */
    public function index()
    {
        return $this->adminIndex();
    }

    /**
     * Display a specific order for admin.
     */
    public function adminShow($id)
    {
        $order = Order::with(['user', 'orderItems.product'])->findOrFail($id);
        return view('admin.orders.show', compact('order'));
    }

    /**
     * Display a specific order.
     */
    public function show($id)
    {
        return $this->adminShow($id);
    }

    /**
     * Update order status.
     */
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|string|in:pending,processing,shipped,delivered,cancelled,refunded',
        ]);

        $order = Order::findOrFail($id);
        $order->status = $request->status;
        $order->save();

        return redirect()->back()->with('success', 'Order status updated successfully.');
    }

    /**
     * Delete an order.
     */
    public function destroy($id)
    {
        $order = Order::findOrFail($id);
        $order->delete();

        return redirect()->route('admin.orders.index')->with('success', 'Order deleted successfully.');
    }
}