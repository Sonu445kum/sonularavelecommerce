@extends('layouts.app')

@section('title', 'My Orders')

@section('content')
<div class="container mx-auto px-4 py-10">
    <h1 class="text-3xl font-bold text-gray-800 mb-8">My Orders</h1>

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
                                @elseif($order->status === 'Shipped') bg-blue-100 text-blue-800
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
                                    $imagePath = $item->product_image
                                        ? asset('storage/' . $item->product_image)
                                        : ($item->product?->image ? asset('storage/' . $item->product->image) : asset('images/no-image.png'));
                                @endphp
                                <img src="{{ $imagePath }}" 
                                     alt="{{ $item->product_name ?? $item->product?->name ?? 'Product' }}"
                                     class="w-12 h-12 rounded-md border object-cover">
                            @endforeach

                            {{-- If more than 3 items --}}
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
            {{ $orders->links('pagination::tailwind') }}
        </div>
    @endif
</div>
@endsection
