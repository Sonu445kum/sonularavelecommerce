@extends('layouts.app')

@section('title', 'Checkout')

@section('content')
<div class="container mx-auto px-4 py-10">

    <h1 class="text-3xl font-bold text-gray-800 mb-8">Checkout</h1>

    <form action="{{ route('orders.store') }}" method="POST">
        @csrf

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            {{-- Left Side: Address + Payment --}}
            <div class="lg:col-span-2 space-y-8">

                {{-- Shipping Address --}}
                <div class="bg-white p-6 rounded-xl shadow-md">
                    <h2 class="text-xl font-semibold text-gray-800 mb-4">Shipping Address</h2>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-gray-600 mb-1 text-sm">Full Name</label>
                            <input type="text" name="name" required 
                                   class="w-full border rounded-md p-2 focus:ring-2 focus:ring-indigo-500">
                        </div>
                        <div>
                            <label class="block text-gray-600 mb-1 text-sm">Email</label>
                            <input type="email" name="email" required 
                                   class="w-full border rounded-md p-2 focus:ring-2 focus:ring-indigo-500">
                        </div>
                        <div>
                            <label class="block text-gray-600 mb-1 text-sm">Phone</label>
                            <input type="text" name="phone" required 
                                   class="w-full border rounded-md p-2 focus:ring-2 focus:ring-indigo-500">
                        </div>
                        <div>
                            <label class="block text-gray-600 mb-1 text-sm">Pincode</label>
                            <input type="text" name="pincode" required 
                                   class="w-full border rounded-md p-2 focus:ring-2 focus:ring-indigo-500">
                        </div>
                        <div class="sm:col-span-2">
                            <label class="block text-gray-600 mb-1 text-sm">Address</label>
                            <textarea name="address" rows="3" required 
                                      class="w-full border rounded-md p-2 focus:ring-2 focus:ring-indigo-500"></textarea>
                        </div>
                    </div>
                </div>

                {{-- Payment Method --}}
                <div class="bg-white p-6 rounded-xl shadow-md">
                    <h2 class="text-xl font-semibold text-gray-800 mb-4">Payment Method</h2>

                    <div class="space-y-4">
                        <label class="flex items-center space-x-3 border rounded-md p-3 cursor-pointer hover:bg-gray-50">
                            <input type="radio" name="payment_method" value="cod" required>
                            <span class="text-gray-800 font-medium">Cash on Delivery (COD)</span>
                        </label>

                        <label class="flex items-center space-x-3 border rounded-md p-3 cursor-pointer hover:bg-gray-50">
                            <input type="radio" name="payment_method" value="razorpay">
                            <span class="text-gray-800 font-medium">Pay Online (Razorpay / Stripe)</span>
                        </label>
                    </div>
                </div>
            </div>

            {{-- Right Side: Order Summary --}}
            <div>
                <div class="bg-gray-50 p-6 rounded-xl shadow-md">
                    <h2 class="text-lg font-semibold text-gray-800 mb-4">Order Summary</h2>

                    <div class="space-y-4">
                        @foreach($cartItems as $item)
                            <div class="flex justify-between items-center border-b pb-3">
                                <div class="flex items-center space-x-3">
                                    <img src="{{ asset('storage/'.$item->product->image) }}" 
                                         alt="{{ $item->product->name }}" 
                                         class="w-12 h-12 rounded-md object-cover">
                                    <div>
                                        <h3 class="font-semibold text-gray-800 text-sm">{{ $item->product->name }}</h3>
                                        <p class="text-xs text-gray-500">Qty: {{ $item->quantity }}</p>
                                    </div>
                                </div>
                                <span class="text-gray-800 font-semibold">
                                    ₹{{ number_format($item->product->price * $item->quantity, 2) }}
                                </span>
                            </div>
                        @endforeach
                    </div>

                    {{-- Summary Total --}}
                    <div class="mt-6 border-t pt-4 space-y-2">
                        <div class="flex justify-between text-gray-700">
                            <span>Subtotal:</span>
                            <span>₹{{ number_format($cartItems->sum(fn($item) => $item->product->price * $item->quantity), 2) }}</span>
                        </div>
                        <div class="flex justify-between text-gray-700">
                            <span>Shipping:</span>
                            <span>₹50.00</span>
                        </div>
                        <div class="flex justify-between font-bold text-gray-900 text-lg border-t pt-2">
                            <span>Total:</span>
                            <span>₹{{ number_format($cartItems->sum(fn($item) => $item->product->price * $item->quantity) + 50, 2) }}</span>
                        </div>
                    </div>

                    {{-- Place Order Button --}}
                    <button type="submit" 
                            class="w-full bg-indigo-600 text-white mt-6 py-3 rounded-lg font-medium hover:bg-indigo-700 transition">
                        Place Order →
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection
