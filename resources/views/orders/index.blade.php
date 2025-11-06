@extends('layouts.app')

@section('title', 'My Orders')

@section('content')
<div class="container mx-auto px-4 py-10">
    <h1 class="text-3xl font-bold text-gray-800 mb-8">My Orders</h1>

    {{-- 🔍 Filter Bar --}}
    <form method="GET" action="{{ route('orders.index') }}" class="mb-8 bg-white shadow-sm rounded-xl p-4 flex flex-wrap gap-4 items-center justify-between">
        <div class="flex items-center gap-3">
            <label for="filter" class="text-gray-700 font-medium">Filter by:</label>
            <select name="filter" id="filter" onchange="this.form.submit()" 
                    class="border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring focus:ring-indigo-200">
                <option value="all" {{ request('filter') == 'all' ? 'selected' : '' }}>All Orders</option>
                <option value="latest" {{ request('filter') == 'latest' ? 'selected' : '' }}>Latest Orders</option>
                <option value="oldest" {{ request('filter') == 'oldest' ? 'selected' : '' }}>Sort by Oldest</option>
                <option value="pending" {{ request('filter') == 'pending' ? 'selected' : '' }}>Pending</option>
                <option value="processing" {{ request('filter') == 'processing' ? 'selected' : '' }}>Processing</option>
                <option value="delivered" {{ request('filter') == 'delivered' ? 'selected' : '' }}>Delivered</option>
            </select>
        </div>

        <a href="{{ route('orders.index') }}" 
           class="text-sm text-indigo-600 hover:underline">Reset Filter</a>
    </form>

    {{-- 🕳️ If no orders --}}
    @if($orders->isEmpty())
        <div class="text-center py-10 bg-white rounded-xl shadow-sm">
            <img src="{{ asset('images/empty-cart.svg') }}" alt="No Orders" class="mx-auto w-40 h-40 mb-4 opacity-80">
            <h2 class="text-xl text-gray-600 mb-2">You haven’t placed any orders yet.</h2>
            <a href="{{ route('home') }}" 
               class="text-indigo-600 font-semibold hover:underline">Start Shopping →</a>
        </div>
    @else
        {{-- 📋 Orders List --}}
        <div class="bg-white rounded-xl shadow-md divide-y">
            @foreach($orders as $order)
                <div class="p-6 flex flex-col sm:flex-row justify-between items-start sm:items-center hover:bg-gray-50 transition">
                    {{-- 🧾 Order Info --}}
                    <div class="flex-1">
                        <div class="flex items-center justify-between sm:justify-start sm:space-x-6">
                            <div>
                                <h2 class="text-lg font-semibold text-indigo-700">
                                    Order #{{ $order->order_number ?? $order->id }}
                                </h2>
                                <p class="text-gray-500 text-sm">
                                    {{ $order->created_at->format('d M Y, h:i A') }}
                                </p>
                            </div>
                            <span class="mt-2 sm:mt-0 px-3 py-1 text-xs font-semibold rounded-full
                                @if($order->status === 'Pending') bg-yellow-100 text-yellow-800
                                @elseif($order->status === 'Processing') bg-blue-100 text-blue-800
                                @elseif($order->status === 'Delivered') bg-green-100 text-green-800
                                @elseif($order->status === 'Cancelled') bg-red-100 text-red-800
                                @else bg-gray-100 text-gray-700
                                @endif">
                                {{ $order->status }}
                            </span>
                        </div>

                        {{-- 🛍️ Order Items Preview --}}
                        <div class="flex flex-wrap items-center gap-3 mt-4">
                            @foreach($order->items->take(3) as $item)
                                @php
                                    $imageUrl = null;
                                    if ($item->product_image) {
                                        $imagePath = $item->product_image;
                                        $imageUrl = (str_starts_with($imagePath, 'http://') || str_starts_with($imagePath, 'https://'))
                                            ? $imagePath
                                            : asset('storage/' . ltrim($imagePath, '/'));
                                    } elseif ($item->product && $item->product->featured_image) {
                                        $imagePath = $item->product->featured_image;
                                        $imageUrl = (str_starts_with($imagePath, 'http://') || str_starts_with($imagePath, 'https://'))
                                            ? $imagePath
                                            : asset('storage/' . ltrim($imagePath, '/'));
                                    } elseif ($item->product && $item->product->images && $item->product->images->count() > 0) {
                                        $imagePath = $item->product->images->first()->path;
                                        $imageUrl = (str_starts_with($imagePath, 'http://') || str_starts_with($imagePath, 'https://'))
                                            ? $imagePath
                                            : asset('storage/' . ltrim($imagePath, '/'));
                                    }
                                    $imageUrl = $imageUrl ?? asset('images/default-product.jpg');
                                    $productName = $item->product_name ?? $item->product->title ?? $item->product->name ?? 'Product';
                                @endphp
                                <img src="{{ $imageUrl }}" 
                                     alt="{{ $productName }}"
                                     class="w-12 h-12 rounded-md border object-cover"
                                     onerror="this.src='{{ asset('images/default-product.jpg') }}'">
                            @endforeach

                            @if($order->items->count() > 3)
                                <span class="text-sm text-gray-500">+{{ $order->items->count() - 3 }} more</span>
                            @endif
                        </div>
                    </div>

                    {{-- 💰 Price + Action --}}
                    <div class="mt-4 sm:mt-0 text-right">
                        <p class="font-bold text-gray-900 text-lg mb-2">
                            ₹{{ number_format($order->total ?? ($order->subtotal + ($order->shipping ?? 50)), 2) }}
                        </p>
                        <a href="{{ route('orders.show', $order->id) }}" 
                           class="inline-block bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700 transition text-sm">
                            View Details
                        </a>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- 📄 Pagination --}}
        <div class="mt-6">
            {{ $orders->appends(request()->query())->links('pagination::tailwind') }}
        </div>
    @endif
</div>
@endsection
