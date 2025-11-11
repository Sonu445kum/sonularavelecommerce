@extends('layouts.app')

@section('title', 'Your Shopping Cart')

@section('content')
<div class="container mx-auto px-4 py-10">

    <h1 class="text-3xl font-bold text-gray-800 mb-8">🛒 Your Shopping Cart</h1>

    {{-- Guest View --}}
    @guest
        <div class="bg-white rounded-xl shadow p-8 text-center">
            <p class="text-gray-600 text-lg mb-4">
                Please <a href="{{ route('login') }}" class="text-indigo-600 hover:underline font-semibold">login</a>
                to view your shopping cart.
            </p>
            <a href="{{ route('products.index') }}" 
               class="bg-indigo-600 text-white px-5 py-2 rounded-lg hover:bg-indigo-700 transition">
               ← Continue Shopping
            </a>
        </div>
    @else
        {{-- Empty Cart --}}
        @if(!$cart || $cart->items->isEmpty())
            <div class="bg-white rounded-xl shadow p-8 text-center">
                <p class="text-gray-500 text-lg mb-4">Your cart is empty.</p>
                <a href="{{ route('products.index') }}" 
                   class="bg-indigo-600 text-white px-5 py-2 rounded-lg hover:bg-indigo-700 transition">
                   Continue Shopping
                </a>
            </div>
        @else
            {{-- Cart Table --}}
            <div class="bg-white rounded-xl shadow-md overflow-x-auto">
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
                        @foreach($cart->items as $item)
                            <tr class="border-b hover:bg-gray-50 transition">
                                {{-- Product Info --}}
                                <td class="p-4 flex items-center space-x-4">
                                    @php
                                        if(!empty($item->product->featured_image_url)){
                                            $imagePath = $item->product->featured_image_url;
                                        } elseif(!empty($item->product->image) && file_exists(public_path('storage/'.$item->product->image))){
                                            $imagePath = asset('storage/'.$item->product->image);
                                        } else {
                                            $imagePath = asset('images/no-image.png');
                                        }
                                    @endphp
                                    <img src="{{ $imagePath }}"
                                         alt="{{ $item->product->name }}"
                                         class="w-16 h-16 object-cover rounded-lg shadow-sm">
                                    <div>
                                        <h3 class="font-semibold text-gray-800">{{ $item->product->name }}</h3>
                                        <p class="text-sm text-gray-500">{{ Str::limit($item->product->description, 60) }}</p>
                                    </div>
                                </td>

                                {{-- Price --}}
                                <td class="p-4 text-center font-semibold text-indigo-600">
                                    ₹{{ number_format($item->price, 2) }}
                                </td>

                                {{-- Quantity Update --}}
                                <td class="p-4 text-center">
                                    <div class="inline-flex items-center justify-center space-x-2">
                                        <button type="button" 
                                                class="quantity-btn bg-gray-200 text-gray-700 px-2 py-1 rounded hover:bg-gray-300"
                                                data-action="decrease"
                                                data-id="{{ $item->id }}">
                                            -
                                        </button>

                                        <input type="number" name="quantity" value="{{ $item->quantity }}" min="1"
                                               class="quantity-input w-16 border border-gray-300 rounded-md p-1 text-center text-gray-700 focus:ring-2 focus:ring-indigo-400 focus:outline-none"
                                               data-id="{{ $item->id }}">

                                        <button type="button" 
                                                class="quantity-btn bg-gray-200 text-gray-700 px-2 py-1 rounded hover:bg-gray-300"
                                                data-action="increase"
                                                data-id="{{ $item->id }}">
                                            +
                                        </button>
                                    </div>
                                </td>

                                {{-- Subtotal --}}
                                <td class="p-4 text-center text-gray-800 font-semibold">
                                    <span class="item-total-price" id="item-total-{{ $item->id }}">
                                        ₹{{ number_format($item->price * $item->quantity, 2) }}
                                    </span>
                                </td>

                                {{-- Remove --}}
                                <td class="p-4 text-center">
                                    <form action="{{ route('cart.remove', $item->id) }}" method="POST" onsubmit="return confirm('Remove this item from cart?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" 
                                                class="text-red-500 hover:text-red-700 font-medium transition">
                                            Remove
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Apply Coupon --}}
            <div class="mt-6 flex items-center gap-4">
                <form action="{{ route('coupon.apply') }}" method="POST" class="flex gap-2">
                    @csrf
                    <input type="text" name="coupon_code" placeholder="Enter coupon code"
                           class="border border-gray-300 p-2 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-400">
                    <button type="submit"
                            class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700 transition">
                        Apply
                    </button>
                </form>
                @if(session('coupon_error'))
                    <span class="text-red-600 font-medium">{{ session('coupon_error') }}</span>
                @endif
                @if(session('coupon_success'))
                    <span class="text-green-600 font-medium">{{ session('coupon_success') }}</span>
                @endif
            </div>

            {{-- Cart Summary & Checkout --}}
            <div class="mt-8 flex flex-col md:flex-row justify-between items-start md:items-start gap-8">
                <a href="{{ route('products.index') }}" 
                   class="text-indigo-600 hover:underline flex items-center gap-1">
                   ← Continue Shopping
                </a>

                <div class="bg-gray-50 p-6 rounded-xl shadow-md w-full md:w-1/3">
                    <h2 class="text-lg font-semibold text-gray-800 mb-4">Order Summary</h2>

                    @php
                        $subtotal = $cart->subtotal ?? 0;
                        $discount = 0;
                        $coupon = session('coupon');
                        if($coupon) {
                            if($coupon['discount_type'] === 'fixed') {
                                $discount = $coupon['discount_value'];
                            } elseif($coupon['discount_type'] === 'percent') {
                                $discount = ($subtotal * $coupon['discount_value']) / 100;
                            }
                        }
                        $shipping = 50;
                        $total = max($subtotal - $discount + $shipping, 0);
                    @endphp

                    <div class="flex justify-between mb-2 text-gray-700">
                        <span>Subtotal:</span>
                        <span class="font-semibold" id="cart-subtotal">₹{{ number_format($subtotal, 2) }}</span>
                    </div>

                    @if($discount > 0)
                        <div class="flex justify-between mb-2 text-green-700 font-medium">
                            <span>Discount ({{ $coupon['code'] ?? '' }}):</span>
                            <span>-₹{{ number_format($discount, 2) }}</span>
                        </div>
                    @endif

                    <div class="flex justify-between mb-2 text-gray-700">
                        <span>Shipping:</span>
                        <span class="font-semibold">₹{{ number_format($shipping, 2) }}</span>
                    </div>

                    <div class="flex justify-between text-xl font-bold text-gray-900 border-t pt-3">
                        <span>Total:</span>
                        <span>₹{{ number_format($total, 2) }}</span>
                    </div>

                    <a href="{{ route('checkout.index') }}" 
                       class="block text-center bg-indigo-600 text-white py-3 mt-6 rounded-lg hover:bg-indigo-700 transition">
                       Proceed to Checkout →
                    </a>
                </div>
            </div>
        @endif
    @endguest

</div>

{{-- AJAX for Quantity Update --}}
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const quantityButtons = document.querySelectorAll('.quantity-btn');
    const quantityInputs = document.querySelectorAll('.quantity-input');

    quantityButtons.forEach(btn => {
        btn.addEventListener('click', function () {
            const action = this.dataset.action;
            const id = this.dataset.id;
            const input = document.querySelector(`.quantity-input[data-id="${id}"]`);
            let qty = parseInt(input.value);

            if(action === 'increase') qty++;
            else if(action === 'decrease' && qty > 1) qty--;

            input.value = qty;
            updateCart(id, qty);
        });
    });

    quantityInputs.forEach(input => {
        input.addEventListener('change', function () {
            const id = this.dataset.id;
            let qty = parseInt(this.value);
            if(qty < 1) qty = 1;
            this.value = qty;
            updateCart(id, qty);
        });
    });

    function updateCart(id, quantity) {
        fetch("/cart/update/" + id, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ quantity: quantity })
        })
        .then(res => res.json())
        .then(data => {
            if(data.status){
                document.getElementById('item-total-' + id).textContent = '₹' + parseFloat(data.item_total).toFixed(2);
                document.getElementById('cart-subtotal').textContent = '₹' + parseFloat(data.cart_subtotal).toFixed(2);
            } else {
                alert(data.message);
            }
        })
        .catch(() => alert('⚠️ Failed to update cart!'));
    }
});

</script>
@endpush
@endsection
