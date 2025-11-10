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
        $order = Order::with(['user', 'orderItems.product', 'address'])->findOrFail($id);

        // 🔹 Pre-calculate each item's subtotal
        $orderSubtotal = 0;
        foreach ($order->orderItems as $item) {
            $itemPrice = $item->price ?? ($item->product->price ?? 0);
            $item->calculated_subtotal = $itemPrice * $item->quantity;
            $orderSubtotal += $item->calculated_subtotal;
        }

        // 🔹 Calculate order totals
        $order->calculated_subtotal = $orderSubtotal;
        $order->calculated_shipping = $order->shipping ?? 0;
        $order->calculated_tax = $order->tax ?? 0;
        $order->calculated_discount = $order->discount ?? 0;
        $order->calculated_total = $orderSubtotal + $order->calculated_shipping + $order->calculated_tax - $order->calculated_discount;

        // 🔹 Get shipping info using accessor (either address relation or shipping_address array)
        $order->shipping_info = $order->shipping_info;

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
