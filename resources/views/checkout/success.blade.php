@extends('layouts.app')

@section('title', 'Payment Success')

@section('content')
<div class="container mx-auto px-6 py-16 flex flex-col items-center justify-center text-center">

    {{-- 🎉 Success Icon --}}
    <div class="bg-green-100 text-green-600 w-20 h-20 flex items-center justify-center rounded-full shadow-md mb-6">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
        </svg>
    </div>

    {{-- ✅ Title --}}
    <h1 class="text-3xl font-bold text-gray-800 mb-3">Payment Successful 🎉</h1>
    <p class="text-gray-600 max-w-lg mb-6">
        Thank you for your purchase! Your payment has been processed successfully and your order has been placed.
    </p>

    {{-- 📦 Order Summary --}}
    <div class="bg-white border rounded-xl shadow-lg p-6 max-w-lg w-full text-left mb-10">
        <h2 class="text-xl font-semibold text-gray-800 mb-4 flex items-center gap-2">
            <i class="bi bi-receipt text-indigo-600"></i> Order Summary
        </h2>

        <div class="space-y-3 text-sm">
            {{-- Order ID --}}
            <div class="flex justify-between">
                <span class="text-gray-600">Order ID:</span>
                <span class="font-semibold text-gray-800">
                    #{{ $order->order_number ?? $order->id ?? 'N/A' }}
                </span>
            </div>

            {{-- Payment Method --}}
            <div class="flex justify-between">
                <span class="text-gray-600">Payment Method:</span>
                <span class="font-semibold text-gray-800 capitalize">
                    {{ $order->payment_method ?? 'Online' }}
                </span>
            </div>

            {{-- Subtotal --}}
            <div class="flex justify-between">
                <span class="text-gray-600">Subtotal:</span>
                <span class="font-semibold text-gray-800">
                    ₹{{ number_format($order->subtotal ?? 0, 2) }}
                </span>
            </div>

            {{-- Coupon Section --}}
            @if(!empty($order->coupon_code))
            <div class="flex justify-between">
                <span class="text-gray-600">Coupon Applied:</span>
                <span class="font-semibold text-indigo-600">{{ $order->coupon_code }}</span>
            </div>
            <div class="flex justify-between">
                <span class="text-gray-600">Discount:</span>
                <span class="font-semibold text-green-600">
                    -₹{{ number_format($order->discount ?? 0, 2) }}
                </span>
            </div>
            @endif

            {{-- Shipping --}}
            <div class="flex justify-between">
                <span class="text-gray-600">Shipping Charges:</span>
                <span class="font-semibold text-gray-800">
                    ₹{{ number_format($order->shipping ?? 0, 2) }}
                </span>
            </div>

            <hr>

            {{-- Total Paid --}}
            <div class="flex justify-between text-base font-semibold">
                <span class="text-gray-700">Total Paid:</span>
                <span class="text-indigo-700">
                    ₹{{ number_format($order->total ?? 0, 2) }}
                </span>
            </div>

            {{-- Order Status --}}
            <div class="flex justify-between">
                <span class="text-gray-600">Order Status:</span>
                <span class="font-semibold text-green-600">
                    {{ ucfirst($order->status ?? 'Success') }}
                </span>
            </div>

            {{-- Payment Status --}}
            <div class="flex justify-between">
                <span class="text-gray-600">Payment Status:</span>
                <span class="font-semibold text-green-600">
                    {{ ucfirst($order->payment_status ?? 'Paid') }}
                </span>
            </div>

            {{-- Paid At --}}
            @if(!empty($order->paid_at))
            <div class="flex justify-between">
                <span class="text-gray-600">Paid On:</span>
                <span class="font-semibold text-gray-800">
                    {{ $order->paid_at->format('d M Y, h:i A') }}
                </span>
            </div>
            @endif
        </div>
    </div>

    {{-- 🔗 Buttons --}}
    <div class="flex flex-col sm:flex-row gap-3">
        <a href="{{ route('orders.index') }}"
           class="bg-indigo-600 text-white px-6 py-3 rounded-lg hover:bg-indigo-700 transition font-medium shadow">
            View My Orders
        </a>
        <a href="{{ route('products.index') }}"
           class="border border-gray-300 text-gray-700 px-6 py-3 rounded-lg hover:bg-gray-100 transition font-medium shadow">
            Continue Shopping
        </a>
    </div>

</div>
@endsection
