@extends('layouts.app')

@section('title', 'Order Details')

@section('content')
<div class="container mx-auto px-4 py-10">
    <div class="bg-white rounded-xl shadow-md p-6">
        {{-- 🧾 Header Section --}}
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Order #{{ $order->order_number ?? $order->id }}</h1>
                <p class="text-gray-500 text-sm">
                    Placed on {{ $order->created_at->format('d M Y, h:i A') }}
                </p>
            </div>
            <span class="mt-3 sm:mt-0 px-3 py-1 text-sm font-semibold rounded-full 
                @if($order->status === 'Pending') bg-yellow-100 text-yellow-800
                @elseif($order->status === 'Shipped') bg-blue-100 text-blue-800
                @elseif($order->status === 'Delivered') bg-green-100 text-green-800
                @elseif($order->status === 'Cancelled') bg-red-100 text-red-800
                @else bg-gray-100 text-gray-800
                @endif">
                {{ $order->status }}
            </span>
        </div>

        {{-- 📦 Shipping Address --}}
        <div class="border rounded-lg p-4 mb-6 bg-gray-50">
            <h2 class="font-semibold text-gray-800 mb-2">Shipping Address</h2>
            <p class="text-gray-700">{{ $order->name ?? 'N/A' }}</p>
            <p class="text-gray-700">{{ $order->address ?? 'N/A' }}</p>
            <p class="text-gray-700">{{ $order->pincode ?? '' }}, {{ $order->phone ?? '' }}</p>
        </div>

        {{-- 🛍️ Order Items --}}
        <h2 class="font-semibold text-gray-800 mb-3">Order Items</h2>
        <div class="divide-y">
            @foreach($order->items as $item)
                <div class="flex justify-between items-center py-4">
                    <div class="flex items-center space-x-4">
                        {{-- ✅ Image Fallback --}}
                        @php
                            $imagePath = $item->product_image 
                                ? asset('storage/' . $item->product_image)
                                : ($item->product?->image ? asset('storage/' . $item->product->image) : asset('images/no-image.png'));
                        @endphp

                        <img src="{{ $imagePath }}" 
                             alt="{{ $item->product_name ?? $item->product?->name ?? 'Product Image' }}" 
                             class="w-16 h-16 rounded-md object-cover border">

                        <div>
                            <p class="font-semibold text-gray-800">
                                {{ $item->product_name ?? $item->product?->name ?? 'Product Name' }}
                            </p>
                            <p class="text-gray-500 text-sm">SKU: {{ $item->product_sku ?? 'N/A' }}</p>
                            <p class="text-gray-500 text-sm">Qty: {{ $item->quantity }}</p>
                        </div>
                    </div>

                    {{-- ✅ Correct price calculation --}}
                    <p class="font-semibold text-gray-800">
                        ₹{{ number_format($item->unit_price * $item->quantity, 2) }}
                    </p>
                </div>
            @endforeach
        </div>

        {{-- 💰 Price Summary --}}
        <div class="border-t mt-6 pt-4 text-gray-700">
            <div class="flex justify-between py-1">
                <span>Subtotal</span>
                <span>₹{{ number_format($order->subtotal, 2) }}</span>
            </div>
            <div class="flex justify-between py-1">
                <span>Shipping</span>
                <span>₹{{ number_format($order->shipping ?? 50, 2) }}</span>
            </div>
            <div class="flex justify-between py-1 font-bold text-gray-900 text-lg">
                <span>Total</span>
                <span>₹{{ number_format($order->total ?? ($order->subtotal + ($order->shipping ?? 50)), 2) }}</span>
            </div>
        </div>

        {{-- 🔙 Footer --}}
        <div class="mt-8 text-right">
            <a href="{{ route('orders.index') }}" 
               class="inline-block bg-gray-200 text-gray-800 px-4 py-2 rounded-md hover:bg-gray-300 transition text-sm">
                ← Back to Orders
            </a>
        </div>
    </div>
</div>
@endsection
