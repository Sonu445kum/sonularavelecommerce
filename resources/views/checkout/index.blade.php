@extends('layouts.app')

@section('title', 'Checkout')

@section('content')
<div class="container mx-auto px-4 py-10">
    <h1 class="text-3xl font-bold text-gray-800 mb-8">Checkout</h1>

    {{-- ✅ Flash Messages --}}
    @if(session('error'))
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded-md mb-4">
            {{ session('error') }}
        </div>
    @endif

    @if(session('success'))
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded-md mb-4">
            {{ session('success') }}
        </div>
    @endif

    {{-- ✅ Empty Cart Handling --}}
    @if(empty($cartItems) || $cartItems->isEmpty())
        <div class="bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700 p-4 rounded-md">
            Your cart is empty.
            <a href="{{ route('products.index') }}" class="underline font-semibold">Shop now →</a>
        </div>
    @else
        <form action="{{ route('checkout.process') }}" method="POST">
            @csrf

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                {{-- =========================================================
                     LEFT SECTION → Shipping Details + Payment Method
                ========================================================= --}}
                <div class="lg:col-span-2 space-y-8">
                    
                    {{-- 🏠 Shipping Address --}}
                    <div class="bg-white p-6 rounded-xl shadow-md">
                        <h2 class="text-xl font-semibold text-gray-800 mb-4">Shipping Address</h2>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-gray-600 mb-1 text-sm">Full Name</label>
                                <input type="text" name="name" value="{{ old('name', auth()->user()->name ?? '') }}" required
                                       class="w-full border rounded-md p-2 focus:ring-2 focus:ring-indigo-500">
                            </div>

                            <div>
                                <label class="block text-gray-600 mb-1 text-sm">Email</label>
                                <input type="email" name="email" value="{{ old('email', auth()->user()->email ?? '') }}" required
                                       class="w-full border rounded-md p-2 focus:ring-2 focus:ring-indigo-500">
                            </div>

                            <div>
                                <label class="block text-gray-600 mb-1 text-sm">Phone</label>
                                <input type="text" name="phone" value="{{ old('phone') }}" required
                                       class="w-full border rounded-md p-2 focus:ring-2 focus:ring-indigo-500">
                            </div>

                            <div>
                                <label class="block text-gray-600 mb-1 text-sm">Pincode</label>
                                <input type="text" name="pincode" value="{{ old('pincode') }}" required
                                       class="w-full border rounded-md p-2 focus:ring-2 focus:ring-indigo-500">
                            </div>

                            <div class="sm:col-span-2">
                                <label class="block text-gray-600 mb-1 text-sm">Address</label>
                                <textarea name="address" rows="3" required
                                          class="w-full border rounded-md p-2 focus:ring-2 focus:ring-indigo-500">{{ old('address') }}</textarea>
                            </div>
                        </div>
                    </div>

                    {{-- 💳 Payment Method --}}
                    <div class="bg-white p-6 rounded-xl shadow-md">
                        <h2 class="text-xl font-semibold text-gray-800 mb-4">Payment Method</h2>

                        <div class="space-y-4">
                            <label class="flex items-center space-x-3 border rounded-md p-3 cursor-pointer hover:bg-gray-50 transition">
                                <input type="radio" name="payment_method" value="cod" required {{ old('payment_method') == 'cod' ? 'checked' : '' }}>
                                <span class="text-gray-800 font-medium">Cash on Delivery (COD)</span>
                            </label>

                            <label class="flex items-center space-x-3 border rounded-md p-3 cursor-pointer hover:bg-gray-50 transition">
                                <input type="radio" name="payment_method" value="card" {{ old('payment_method') == 'card' ? 'checked' : '' }}>
                                <span class="text-gray-800 font-medium">Pay Online (Stripe)</span>
                            </label>
                        </div>
                    </div>
                </div>

                {{-- =========================================================
                     RIGHT SECTION → Order Summary
                ========================================================= --}}
                <div>
                    <div class="bg-gray-50 p-6 rounded-xl shadow-md">
                        <h2 class="text-lg font-semibold text-gray-800 mb-4">Order Summary</h2>

                        {{-- 🛒 Cart Items --}}
                        <div class="space-y-4 max-h-80 overflow-y-auto pr-2">
                            @foreach($cartItems as $item)
                                @php
                                    // Try featured_image first, then first image from images relationship, then fallback
                                    $imageUrl = null;
                                    if ($item->product) {
                                        // Check featured_image
                                        if ($item->product->featured_image) {
                                            $imagePath = $item->product->featured_image;
                                            $imageUrl = (str_starts_with($imagePath, 'http://') || str_starts_with($imagePath, 'https://'))
                                                ? $imagePath
                                                : asset('storage/' . ltrim($imagePath, '/'));
                                        }
                                        // Fallback to first image in images relationship
                                        elseif ($item->product->images && $item->product->images->count() > 0) {
                                            $imagePath = $item->product->images->first()->path;
                                            $imageUrl = (str_starts_with($imagePath, 'http://') || str_starts_with($imagePath, 'https://'))
                                                ? $imagePath
                                                : asset('storage/' . ltrim($imagePath, '/'));
                                        }
                                    }
                                    // Final fallback
                                    $imageUrl = $imageUrl ?? asset('images/default-product.jpg');
                                    $productName = $item->product->title ?? $item->product->name ?? 'Unnamed Product';
                                @endphp

                                <div class="flex justify-between items-center border-b pb-3">
                                    <div class="flex items-center space-x-3">
                                        <img src="{{ $imageUrl }}" alt="{{ $productName }}"
                                             class="w-12 h-12 rounded-md object-cover"
                                             onerror="this.src='{{ asset('images/default-product.jpg') }}'">

                                        <div>
                                            <h3 class="font-semibold text-gray-800 text-sm">{{ $productName }}</h3>
                                            <p class="text-xs text-gray-500">Qty: {{ $item->quantity }}</p>
                                        </div>
                                    </div>
                                    <span class="text-gray-800 font-semibold">
                                        ₹{{ number_format($item->price * $item->quantity, 2) }}
                                    </span>
                                </div>
                            @endforeach
                        </div>

                        {{-- 💰 Order Totals --}}
                        @php
                            $subtotal = $cartItems->sum(fn($item) => $item->price * $item->quantity);
                            $shipping = 50;
                            $discount = 0;

                            if (session('coupon')) {
                                $coupon = session('coupon');
                                $discount = ($coupon['discount_type'] === 'fixed')
                                    ? $coupon['value']
                                    : ($subtotal * $coupon['value'] / 100);
                            }

                            $total = max($subtotal - $discount + $shipping, 0);
                        @endphp

                        {{-- 🎟️ Coupon Input --}}
                        <form action="{{ route('checkout.applyCoupon') }}" method="POST" class="mt-4">
                            @csrf
                            <div class="flex items-center space-x-2">
                                <input type="text" name="code" placeholder="Enter coupon code"
                                       class="w-full border rounded-md p-2 focus:ring-2 focus:ring-indigo-500"
                                       value="{{ old('code', session('coupon.code') ?? '') }}">
                                <button type="submit"
                                        class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700">
                                    Apply
                                </button>
                            </div>

                            @if(session('coupon'))
                                <p class="text-green-600 text-sm mt-1">
                                    ✅ Coupon Applied: <strong>{{ session('coupon.code') }}</strong>
                                </p>
                            @endif
                        </form>

                        {{-- 📦 Total Summary --}}
                        <div class="mt-6 border-t pt-4 space-y-2 text-sm">
                            <div class="flex justify-between text-gray-700">
                                <span>Subtotal:</span>
                                <span>₹{{ number_format($subtotal, 2) }}</span>
                            </div>

                            @if($discount > 0)
                                <div class="flex justify-between text-green-700">
                                    <span>Discount ({{ session('coupon.code') ?? '' }}):</span>
                                    <span>- ₹{{ number_format($discount, 2) }}</span>
                                </div>
                            @endif

                            <div class="flex justify-between text-gray-700">
                                <span>Shipping:</span>
                                <span>₹{{ number_format($shipping, 2) }}</span>
                            </div>

                            <div class="flex justify-between font-bold text-gray-900 text-lg border-t pt-2">
                                <span>Total:</span>
                                <span>₹{{ number_format($total, 2) }}</span>
                            </div>
                        </div>

                        {{-- ✅ Place Order --}}
                        <button type="submit"
                                class="w-full bg-indigo-600 text-white mt-6 py-3 rounded-lg font-medium hover:bg-indigo-700 transition">
                            Place Order →
                        </button>
                    </div>
                </div>
            </div>
        </form>
    @endif
</div>
@endsection
