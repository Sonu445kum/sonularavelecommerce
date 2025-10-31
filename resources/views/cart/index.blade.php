@extends('layouts.app')

@section('title', 'Your Shopping Cart')

@section('content')
<div class="container mx-auto px-4 py-10">

    <h1 class="text-3xl font-bold text-gray-800 mb-8">🛒 Your Shopping Cart</h1>

    {{-- If Cart is Empty --}}
    @if($cartItems->isEmpty())
        <div class="bg-white rounded-xl shadow p-8 text-center">
            <p class="text-gray-500 text-lg mb-4">Your cart is empty.</p>
            <a href="{{ route('products.index') }}" 
               class="bg-indigo-600 text-white px-5 py-2 rounded-lg hover:bg-indigo-700 transition">
               Continue Shopping
            </a>
        </div>
    @else
        {{-- Cart Table --}}
        <div class="bg-white rounded-xl shadow-md overflow-hidden">
            <table class="min-w-full text-left border-collapse">
                <thead class="bg-gray-100 text-gray-700 uppercase text-sm">
                    <tr>
                        <th class="p-4">Product</th>
                        <th class="p-4 text-center">Price</th>
                        <th class="p-4 text-center">Quantity</th>
                        <th class="p-4 text-center">Subtotal</th>
                        <th class="p-4 text-center">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($cartItems as $item)
                        <tr class="border-b hover:bg-gray-50 transition">
                            {{-- Product Info --}}
                            <td class="p-4 flex items-center space-x-4">
                                <img src="{{ asset('storage/'.$item->product->image) }}" 
                                     alt="{{ $item->product->name }}" 
                                     class="w-16 h-16 object-cover rounded-lg shadow-sm">
                                <div>
                                    <h3 class="font-semibold text-gray-800">{{ $item->product->name }}</h3>
                                    <p class="text-sm text-gray-500">{{ Str::limit($item->product->description, 60) }}</p>
                                </div>
                            </td>

                            {{-- Price --}}
                            <td class="p-4 text-center font-semibold text-indigo-600">
                                ₹{{ number_format($item->product->price, 2) }}
                            </td>

                            {{-- Quantity Update --}}
                            <td class="p-4 text-center">
                                <form action="{{ route('cart.update', $item->id) }}" method="POST" class="inline-flex items-center justify-center">
                                    @csrf
                                    @method('PUT')
                                    <input type="number" name="quantity" value="{{ $item->quantity }}" min="1"
                                           class="w-16 border rounded-md p-1 text-center text-gray-700">
                                    <button type="submit" 
                                            class="ml-2 bg-green-500 text-white px-3 py-1 rounded hover:bg-green-600 text-sm">
                                        Update
                                    </button>
                                </form>
                            </td>

                            {{-- Subtotal --}}
                            <td class="p-4 text-center text-gray-800 font-semibold">
                                ₹{{ number_format($item->product->price * $item->quantity, 2) }}
                            </td>

                            {{-- Remove Button --}}
                            <td class="p-4 text-center">
                                <form action="{{ route('cart.remove', $item->id) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" 
                                            class="text-red-500 hover:text-red-700 font-medium">
                                        Remove
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Cart Summary --}}
        <div class="mt-8 flex flex-col md:flex-row justify-between items-start md:items-center">
            <a href="{{ route('products.index') }}" 
               class="text-indigo-600 hover:underline mb-4 md:mb-0">
               ← Continue Shopping
            </a>

            <div class="bg-gray-50 p-6 rounded-xl shadow-md w-full md:w-1/3">
                <h2 class="text-lg font-semibold text-gray-800 mb-4">Order Summary</h2>
                <div class="flex justify-between mb-2">
                    <span class="text-gray-600">Subtotal:</span>
                    <span class="font-semibold text-gray-800">
                        ₹{{ number_format($cartItems->sum(fn($item) => $item->product->price * $item->quantity), 2) }}
                    </span>
                </div>
                <div class="flex justify-between mb-4">
                    <span class="text-gray-600">Shipping:</span>
                    <span class="text-gray-800 font-semibold">₹50.00</span>
                </div>
                <div class="flex justify-between text-xl font-bold text-gray-900 border-t pt-3">
                    <span>Total:</span>
                    <span>
                        ₹{{ number_format($cartItems->sum(fn($item) => $item->product->price * $item->quantity) + 50, 2) }}
                    </span>
                </div>

                <a href="{{ route('checkout.index') }}" 
                   class="block text-center bg-indigo-600 text-white py-3 mt-6 rounded-lg hover:bg-indigo-700 transition">
                   Proceed to Checkout →
                </a>
            </div>
        </div>
    @endif

</div>
@endsection
