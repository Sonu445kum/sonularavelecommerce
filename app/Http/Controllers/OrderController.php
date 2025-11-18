<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Order;
use App\Models\Review;

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
                $query->orderBy('created_at', 'desc');
                break;
        }

        // 🔹 Pagination
        $orders = $query->paginate($user->is_admin ? 20 : 10);

        if ($orders->isEmpty()) {
            session()->flash('info', 'No orders found for this filter.');
        }

        return view('orders.index', compact('orders'));
    }

    /**
     * ==========================================
     * Display Single Order Details + Reviews
     * ==========================================
     */
    public function show($id)
    {
        $order = Order::with([
            'user',
            'items.product.images',
            'address',
            'payments' => function ($q) {
                $q->latest();
            }
        ])->findOrFail($id);

        // 🔒 Access Control
        if (Auth::id() !== $order->user_id && !Auth::user()?->is_admin) {
            abort(403, 'Unauthorized access to order details.');
        }

        // 🩵 Ensure Address Relation
        if (!$order->address && $order->address_id) {
            $order->load('address');
        }

        // // 🧠 Normalize Shipping Address
        // if ($order->address) {
        //     $order->shipping_address = (object) [
        //         'name' => $order->address->name ?? $order->user->name ?? 'N/A',
        //         'phone' => $order->address->phone ?? $order->user->phone ?? 'N/A',
        //         'address_line1' => $order->address->address_line1 ?? $order->address->address ?? null,
        //         'city' => $order->address->city ?? null,
        //         'state' => $order->address->state ?? null,
        //         'postal_code' => $order->address->postal_code ?? null,
        //         'country' => $order->address->country ?? 'India',
        //     ];
        // } elseif (!empty($order->shipping_address)) {
        //     if (is_array($order->shipping_address)) {
        //         $order->shipping_address = (object) $order->shipping_address;
        //     } elseif (is_string($order->shipping_address)) {
        //         $decoded = json_decode($order->shipping_address, true);
        //         $order->shipping_address = is_array($decoded) ? (object) $decoded : (object)[];
        //     }
        // } else {
        //     $order->shipping_address = (object) [
        //         'name' => $order->name ?? $order->user->name ?? 'N/A',
        //         'phone' => $order->phone ?? $order->user->phone ?? 'N/A',
        //         'address_line1' => $order->address_line1 ?? $order->address ?? null,
        //         'city' => $order->city ?? null,
        //         'state' => $order->state ?? null,
        //         'postal_code' => $order->postal_code ?? $order->pincode ?? null,
        //         'country' => $order->country ?? 'India',
        //     ];
        // }

        // 🧾 Format Data
        $order->formatted_total = number_format($order->total, 2);
        $order->item_count = $order->items->sum('quantity');

        // 🔹 Fetch reviews for all products in this order
        $productIds = $order->items->pluck('product_id')->toArray();
        $reviews = Review::with('user')
            ->whereIn('product_id', $productIds)
            ->latest()
            ->paginate(3); // Pagination

        // 🔹 Pass first product for review form if needed
        $firstProduct = $order->items->first()?->product;

        return view('orders.show', compact('order', 'firstProduct', 'reviews'));
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
