<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Order;

class OrderController extends Controller
{
    /**
     * ==========================================
     * Display All Orders (User / Admin) + Filter
     * ==========================================
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        if (!$user) {
            return redirect()->route('login')->with('error', 'Please login to view your orders.');
        }

        // 🔹 Base query for admin or user
        $query = $user->is_admin
            ? Order::with(['user', 'items.product.images', 'address'])
            : $user->orders()->with(['items.product.images', 'address']);

        // 🔍 Apply Filter Logic
        switch ($request->filter) {
            case 'latest':
                $query->orderBy('created_at', 'desc');
                break;

            case 'oldest':
                $query->orderBy('created_at', 'asc');
                break;

            case 'pending':
                $query->where('status', 'Pending');
                break;

            case 'processing':
                $query->where('status', 'Processing');
                break;

            case 'delivered':
                $query->where('status', 'Delivered');
                break;

            default:
                $query->orderBy('created_at', 'desc'); // Default to latest
                break;
        }

        // 🔹 Pagination
        $orders = $query->paginate($user->is_admin ? 20 : 10);

        // 🔹 Handle empty orders
        if ($orders->isEmpty()) {
            session()->flash('info', 'No orders found for this filter.');
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
            'items.product.images',
             'items.product.reviews' => function ($query) {
                $query->where('user_id', Auth::id());
            },
            'address',
            'payments' => function ($q) {
                $q->latest();
            }
        ])->findOrFail($id);

        // 🔒 Access Control
        if (Auth::id() !== $order->user_id && !Auth::user()?->is_admin) {
            abort(403, 'Unauthorized access to order details.');
        }

        // 🔹 Ensure Address Relation (Fallback)
        if (!$order->address && $order->address_id) {
            $order->load('address');
        }

        // 🔹 Optional Fallback (legacy orders)
        $order->shipping_address = $order->address
            ? $order->address
            : (object) [
                'name' => $order->name,
                'phone' => $order->phone,
                'address_line1' => $order->address_line1 ?? $order->address ?? null,
                'address_line2' => $order->address_line2 ?? null,
                'city' => $order->city ?? null,
                'state' => $order->state ?? null,
                'postal_code' => $order->postal_code ?? $order->pincode ?? null,
                'country' => $order->country ?? 'India',
                'label' => 'Default',
            ];

        // 🔹 Format Data
        $order->formatted_total = number_format($order->total, 2);
        $order->item_count = $order->items->sum('quantity');

        return view('orders.show', compact('order'));
    }

    /**
     * ==========================================
     * Cancel Order (User / Admin)
     * ==========================================
     */
    public function cancel($id)
    {
        $order = Order::findOrFail($id);

        if (Auth::id() !== $order->user_id && !Auth::user()?->is_admin) {
            abort(403, 'Unauthorized to cancel this order.');
        }

        if (in_array(strtolower($order->status), ['shipped', 'delivered'])) {
            return back()->with('error', 'You cannot cancel an order that is already shipped or delivered.');
        }

        $order->status = 'Cancelled';
        $order->save();

        return redirect()->route('orders.index')->with('success', 'Order has been cancelled successfully.');
    }
}
