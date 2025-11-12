@extends('layouts.app')

@section('title', 'Your Shopping Cart')

@section('content')
<div class="container mx-auto px-4 py-10">

    <h1 class="text-3xl font-bold text-gray-800 mb-8">🛒 Your Shopping Cart</h1>

    {{-- Toast Container --}}
    <div id="toast-container" class="fixed top-5 right-5 space-y-2 z-50"></div>

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
                                <td class="p-4 flex items-center space-x-4">
                                    @php
                                        $imagePath = $item->product->featured_image_url 
                                                    ?? (file_exists(public_path('storage/'.$item->product->image)) 
                                                        ? asset('storage/'.$item->product->image) 
                                                        : asset('images/no-image.png'));
                                    @endphp
                                    <img src="{{ $imagePath }}" alt="{{ $item->product->name }}" 
                                         class="w-16 h-16 object-cover rounded-lg shadow-sm">
                                    <div>
                                        <h3 class="font-semibold text-gray-800">{{ $item->product->name }}</h3>
                                        <p class="text-sm text-gray-500">{{ Str::limit($item->product->description, 60) }}</p>
                                    </div>
                                </td>

                                <td class="p-4 text-center font-semibold text-indigo-600">
                                    ₹{{ number_format($item->price, 2) }}
                                </td>

                                <td class="p-4 text-center">
                                    <div class="flex items-center border border-gray-300 rounded-md overflow-hidden inline-flex justify-center mx-auto">
                                        <button type="button" 
                                                class="px-2 py-1 bg-gray-200 hover:bg-gray-300 text-gray-700 decrement"
                                                data-item-id="{{ $item->id }}">-</button>
                                        <input type="number" value="{{ $item->quantity }}" min="1" 
                                            class="w-12 text-center border-l border-r border-gray-300 focus:outline-none quantity-input"
                                            data-item-id="{{ $item->id }}">
                                        <button type="button" 
                                                class="px-2 py-1 bg-gray-200 hover:bg-gray-300 text-gray-700 increment"
                                                data-item-id="{{ $item->id }}">+</button>
                                    </div>
                                </td>

                                <td class="p-4 text-center text-gray-800 font-semibold subtotal-cell">
                                    ₹{{ number_format($item->price * $item->quantity, 2) }}
                                </td>

                               <td class="p-4 text-center">
                                <form class="remove-item-form" data-item-id="{{ $item->id }}" onsubmit="return false;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-500 hover:text-red-700 font-medium transition">
                                        Remove
                                    </button>
                                </form>
                            </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Cart Summary & Coupon --}}
            <div class="mt-8 flex flex-col md:flex-row justify-between items-start md:items-start gap-8">
                <a href="{{ route('products.index') }}" 
                   class="text-indigo-600 hover:underline flex items-center gap-1">
                    ← Continue Shopping
                </a>

                <div class="bg-gray-50 p-6 rounded-xl shadow-md w-full md:w-1/3">
                    <h2 class="text-lg font-semibold text-gray-800 mb-4">Apply Coupon</h2>

                    {{-- Coupon Form --}}
                    <form id="coupon-form" class="flex items-center gap-2 mb-4" onsubmit="return false;">
                        @csrf
                        <input type="text" name="coupon_code" placeholder="Enter coupon code"
                               class="border border-gray-300 rounded-md p-2 w-full focus:ring-2 focus:ring-indigo-400 focus:outline-none"
                               required>
                        <button type="submit" 
                                class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700 transition">
                            Apply
                        </button>
                    </form>

                    {{-- Remove Coupon --}}
                    @if(session('coupon'))
                        <button id="remove-coupon-btn" class="text-red-600 hover:underline mb-4">
                            Remove Coupon
                        </button>
                    @endif

                    <div id="discountInfo" class="mt-3"></div>

                    {{-- 🧮 Summary Section --}}
                    @php
                        $subtotal = $cart->items->sum(fn($item) => $item->price * $item->quantity);
                        $discount = 0;
                        $coupon = session('coupon');
                        $couponCode = $coupon['code'] ?? null;

                        if ($coupon) {
                            if ($coupon['type'] === 'fixed') $discount = $coupon['value'];
                            elseif ($coupon['type'] === 'percent') $discount = ($subtotal * $coupon['value']) / 100;
                        }

                        $shipping = 50;
                        $total = max(($subtotal - $discount) + $shipping, 0);
                    @endphp

                    <div class="border-t mt-4 pt-4 space-y-2 text-gray-700">
                        <div class="flex justify-between">
                            <span>Subtotal:</span>
                            <span id="cart-subtotal" class="font-semibold">₹{{ number_format($subtotal, 2) }}</span>
                        </div>

                        @if($discount > 0)
                            <div class="flex justify-between text-green-700 font-medium">
                                <span>Discount ({{ $couponCode }}):</span>
                                <span id="cart-discount">-₹{{ number_format($discount, 2) }}</span>
                            </div>
                        @endif

                        <div class="flex justify-between">
                            <span>Shipping:</span>
                            <span class="font-semibold">₹{{ number_format($shipping, 2) }}</span>
                        </div>

                        <div class="flex justify-between text-xl font-bold text-gray-900 border-t pt-3">
                            <span>Total:</span>
                            <span id="cart-total">₹{{ number_format($total, 2) }}</span>
                        </div>
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

{{-- Axios --}}
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {

    // ======= Toast =======
    function showToast(message, type = 'success') {
        const toast = document.createElement('div');
        toast.className = `max-w-xs w-full ${type==='success' ? 'bg-green-500' : 'bg-red-500'} text-white px-4 py-3 rounded shadow-lg animate-fade-in`;
        toast.innerText = message;
        document.getElementById('toast-container').appendChild(toast);
        setTimeout(() => toast.remove(), 4000);
    }

    // ======= Quantity Update =======
    document.querySelectorAll('.decrement, .increment').forEach(btn => {
        btn.addEventListener('click', async function() {
            const itemId = this.dataset.itemId;
            const input = document.querySelector(`.quantity-input[data-item-id='${itemId}']`);
            let quantity = parseInt(input.value);
            if(this.classList.contains('decrement') && quantity>1) quantity--;
            if(this.classList.contains('increment')) quantity++;
            input.value = quantity;

            try {
                const res = await axios.post(`/cart/${itemId}/update`, { quantity }, {
                    headers: { 'X-CSRF-TOKEN': "{{ csrf_token() }}" }
                });
                if(res.data.success){
                    const subtotalCell = input.closest('tr').querySelector('.subtotal-cell');
                    if(subtotalCell) subtotalCell.textContent = `₹${res.data.itemSubtotal.toFixed(2)}`;

                    const subtotalElem = document.getElementById('cart-subtotal');
                    if(subtotalElem) subtotalElem.textContent = `₹${res.data.cartSubtotal.toFixed(2)}`;

                    const cartTotalElem = document.getElementById('cart-total');
                    if(cartTotalElem) cartTotalElem.textContent = `₹${res.data.total.toFixed(2)}`;

                    const discountElem = document.getElementById('cart-discount');
                    if(discountElem && res.data.discount > 0){
                        discountElem.textContent = `-₹${res.data.discount.toFixed(2)}`;
                    }

                    showToast('✅ Quantity updated!');
                }

            } catch(err){
                console.error('Cart update error:', err);
                showToast('❌ Something went wrong!', 'error');
            }
        });
    });

    // Remove items From the Card
    document.querySelectorAll('.remove-item-form').forEach(form => {
    form.addEventListener('submit', async function() {
        if(!confirm('Remove this item from cart?')) return;

        const itemId = this.dataset.itemId;
        const token = this.querySelector('input[name="_token"]').value;

        try {
            const res = await axios.post(`/cart/remove/${itemId}`, {
                _method: 'DELETE' // Laravel ko DELETE request samjhaye
            }, {
                headers: { 'X-CSRF-TOKEN': token }
            });

            if(res.status === 200){
                // Remove the row from table
                const row = this.closest('tr');
                if(row) row.remove();

                // Update cart totals if sent from backend (optional)
                if(res.data.cartSubtotal !== undefined){
                    const subtotalElem = document.getElementById('cart-subtotal');
                    if(subtotalElem) subtotalElem.textContent = `₹${res.data.cartSubtotal.toFixed(2)}`;
                }

                if(res.data.total !== undefined){
                    const totalElem = document.getElementById('cart-total');
                    if(totalElem) totalElem.textContent = `₹${res.data.total.toFixed(2)}`;
                }

                showToast('🗑️ Item removed successfully!');
            }

        } catch(err){
            console.error('Remove item error:', err);
            showToast('❌ Failed to remove item', 'error');
        }
    });
});





    // ======= Coupon Apply =======
    const couponForm = document.getElementById("coupon-form");
    const removeCouponBtn = document.getElementById("remove-coupon-btn");
    const discountInfo = document.getElementById("discountInfo");

    if(couponForm){
        couponForm.addEventListener("submit", async function(e){
            e.preventDefault();
            const code = this.querySelector('input[name="coupon_code"]').value.trim();
            if(!code) return alert('⚠️ Enter coupon code');

            try {
                const res = await axios.post("{{ route('coupon.apply') }}", { coupon_code: code }, {
                    headers: { "X-CSRF-TOKEN": "{{ csrf_token() }}" }
                });

                if(res.data.success){
                    discountInfo.innerHTML = `<p class="text-green-600 font-semibold">${res.data.message}</p>`;
                    const discountElem = document.getElementById('cart-discount');
                    if(discountElem) discountElem.textContent = `-₹${res.data.discount.toFixed(2)}`;

                    const cartTotalElem = document.getElementById('cart-total');
                    if(cartTotalElem) cartTotalElem.textContent = `₹${res.data.newTotal.toFixed(2)}`;
                    showToast(res.data.message, 'success');
                } else {
                    discountInfo.innerHTML = `<p class="text-red-600 font-semibold">${res.data.message}</p>`;
                    showToast(res.data.message, 'error');
                }

            } catch(err){
                console.error("Coupon apply error:", err);
                discountInfo.innerHTML = `<p class="text-red-600 font-semibold">❌ Something went wrong!</p>`;
                showToast('❌ Coupon apply failed!', 'error');
            }
        });
    }

    // ======= Coupon Remove =======
    if(removeCouponBtn){
        removeCouponBtn.addEventListener('click', async () => {
            try {
                const res = await axios.post("{{ route('coupon.remove') }}", {}, {
                    headers: { "X-CSRF-TOKEN": "{{ csrf_token() }}" }
                });
                if(res.data.success){
                    discountInfo.innerHTML = `<p class="text-green-600 font-semibold">${res.data.message}</p>`;

                    const discountElem = document.getElementById('cart-discount');
                    if(discountElem) discountElem.textContent = `-₹0.00`;

                    const cartTotalElem = document.getElementById('cart-total');
                    if(cartTotalElem) cartTotalElem.textContent = `₹${res.data.newTotal.toFixed(2)}`;
                    showToast(res.data.message, 'success');
                } else {
                    discountInfo.innerHTML = `<p class="text-red-600 font-semibold">${res.data.message}</p>`;
                    showToast(res.data.message, 'error');
                }
            } catch(err){
                console.error("Coupon remove error:", err);
                discountInfo.innerHTML = `<p class="text-red-600 font-semibold">❌ Something went wrong!</p>`;
                showToast('❌ Coupon remove failed!', 'error');
            }
        });
    }

});
</script>

<style>
@keyframes fade-in { from {opacity:0; transform:translateY(-10px);} to{opacity:1; transform:translateY(0);} }
.animate-fade-in { animation: fade-in 0.5s ease forwards; }
</style>

@endsection
