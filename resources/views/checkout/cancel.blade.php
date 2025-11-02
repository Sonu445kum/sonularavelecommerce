@extends('layouts.app')

@section('title', 'Payment Cancelled')

@section('content')
<div class="container mx-auto px-6 py-16 flex flex-col items-center justify-center text-center">

    {{-- ❌ Cancel Icon --}}
    <div class="bg-red-100 text-red-600 w-20 h-20 flex items-center justify-center rounded-full shadow-md mb-6">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M6 18L18 6M6 6l12 12" />
        </svg>
    </div>

    {{-- ❗ Title --}}
    <h1 class="text-3xl font-bold text-gray-800 mb-3">Payment Cancelled</h1>
    <p class="text-gray-600 max-w-lg mb-6">
        Your payment has been cancelled or was not completed. Don’t worry — you can try again anytime.
    </p>

    {{-- 📦 Order Info (Optional Display) --}}
    @if(isset($order))
    <div class="bg-white border rounded-xl shadow-md p-6 max-w-md w-full text-left mb-8">
        <h2 class="text-lg font-semibold text-gray-700 mb-4">Order Information</h2>
        <div class="space-y-3 text-sm">
            <div class="flex justify-between">
                <span class="text-gray-600">Order ID:</span>
                <span class="font-semibold text-gray-800">{{ $order->order_number ?? $order->id ?? 'N/A' }}</span>
            </div>
            <div class="flex justify-between">
                <span class="text-gray-600">Amount:</span>
                <span class="font-semibold text-gray-800">₹{{ number_format($order->total ?? 0, 2) }}</span>
            </div>
            <div class="flex justify-between">
                <span class="text-gray-600">Status:</span>
                <span class="font-semibold text-red-600">{{ ucfirst($order->status ?? 'Cancelled') }}</span>
            </div>
        </div>
    </div>
    @endif

    {{-- 🔗 Buttons --}}
    <div class="flex flex-col sm:flex-row gap-3">
        <a href="{{ route('checkout.index') }}"
           class="bg-red-600 text-white px-6 py-3 rounded-lg hover:bg-red-700 transition font-medium">
            Try Again
        </a>
        <a href="{{ route('products.index') }}"
           class="border border-gray-300 text-gray-700 px-6 py-3 rounded-lg hover:bg-gray-100 transition font-medium">
            Continue Shopping
        </a>
    </div>

</div>
@endsection
