@extends('layouts.app')

@section('title', 'Checkout')

@section('content')
<div class="container my-5">

    <h2 class="mb-4 fw-bold">Checkout</h2>

    {{-- Display validation errors --}}
    @if($errors->any())
        <div class="alert alert-danger">
            @foreach($errors->all() as $error) 
                <div>{{ $error }}</div> 
            @endforeach
        </div>
    @endif

    {{-- Display success message --}}
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="row g-4">

        {{-- Cart Items --}}
        <div class="col-lg-7">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header bg-light fw-bold fs-5">Your Cart</div>
                <div class="card-body">

                    @php
                        // Use Cart subtotal and items from controller
                        $subtotal = $cart->subtotal ?? 0;
                        $cartItems = $cart->items ?? collect([]);
                    @endphp

                    @if($cartItems->isNotEmpty())
                        @foreach ($cartItems as $item)
                            @php
                                $product = $item->product ?? null;

                                // Image handling
                                if (!empty($item->product->featured_image_url)) {
                                    $image = $item->product->featured_image_url;
                                } elseif (!empty($item->product->image) && file_exists(public_path('storage/'.$item->product->image))) {
                                    $image = asset('storage/'.$item->product->image);
                                } else {
                                    $image = asset('images/no-image.png');
                                }

                                $price = $item->price ?? ($product->price ?? 0);
                                $itemSubtotal = $price * ($item->quantity ?? 1);
                            @endphp

                            <div class="d-flex align-items-center mb-3 border-bottom pb-2">
                                <img src="{{ $image }}" 
                                     class="rounded-3 me-3" 
                                     style="width: 70px; height: 70px; object-fit: cover;" 
                                     alt="{{ $product->name ?? $item->product_name ?? 'Product' }}">
                                <div class="flex-grow-1">
                                    <h6 class="fw-semibold mb-1">{{ $product->name ?? $item->product_name ?? 'Product Name' }}</h6>
                                    <p class="text-muted mb-1">Qty: {{ $item->quantity ?? 1 }}</p>
                                    <p class="fw-bold mb-0">₹{{ number_format($itemSubtotal, 2) }}</p>
                                </div>
                            </div>
                        @endforeach

                        <div class="text-end fw-bold fs-5 mt-3">
                            Subtotal: ₹{{ number_format($subtotal, 2) }}
                        </div>
                    @else
                        <p class="text-muted text-center mb-0">Your cart is empty.</p>
                    @endif

                </div>
            </div>
        </div>

        {{-- Shipping, Payment & Coupon Summary --}}
        <div class="col-lg-5">

            @php
                $coupon = session('coupon');
                $discount = 0;

                // Calculate discount if coupon applied
                if($coupon) {
                    if($coupon['discount_type'] === 'fixed') $discount = $coupon['discount_value'];
                    elseif($coupon['discount_type'] === 'percent') $discount = ($subtotal * $coupon['discount_value']) / 100;
                }

                $shipping = 50;
                $total = max($subtotal - $discount + $shipping, 0);
            @endphp

            {{-- Coupon banner --}}
            @if($coupon)
                <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-3 rounded mb-4">
                    ✅ Coupon "<strong>{{ $coupon['code'] }}</strong>" applied successfully! 
                    <form action="{{ route('coupon.remove') }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="text-red-600 font-medium ml-2 hover:underline">
                            Remove
                        </button>
                    </form>
                </div>
            @endif

            {{-- Checkout Form --}}
            <form action="{{ route('checkout.process') }}" method="POST" class="card border-0 shadow-sm rounded-4 p-4 mt-3">
                @csrf

                <h5 class="mb-3 fw-bold">Shipping Details</h5>

                <div class="mb-3">
                    <label class="form-label">Name</label>
                    <input type="text" name="name" class="form-control" value="{{ old('name', auth()->user()->name ?? '') }}" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-control" value="{{ old('email', auth()->user()->email ?? '') }}" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Phone</label>
                    <input type="text" name="phone" class="form-control" value="{{ old('phone') }}" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Pincode</label>
                    <input type="text" name="pincode" class="form-control" value="{{ old('pincode') }}" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Address</label>
                    <textarea name="address" class="form-control" rows="2" required>{{ old('address') }}</textarea>
                </div>

                <div class="mb-3">
                    <label class="form-label">City</label>
                    <input type="text" name="city" class="form-control" value="{{ old('city') }}">
                </div>

                <div class="mb-3">
                    <label class="form-label">State</label>
                    <input type="text" name="state" class="form-control" value="{{ old('state') }}">
                </div>

                <hr>

                <h5 class="mb-3 fw-bold">Payment Method</h5>

                <div class="mb-3 form-check">
                    <input type="radio" name="payment_method" id="cod" value="cod" class="form-check-input" checked>
                    <label for="cod" class="form-check-label">Cash on Delivery</label>
                </div>

                <div class="mb-3 form-check">
                    <input type="radio" name="payment_method" id="card" value="card" class="form-check-input">
                    <label for="card" class="form-check-label">Card / Online Payment</label>
                </div>

                {{-- Total Amount (from Cart) --}}
                <div class="mb-3 fw-bold fs-5 text-end">
                    Total: ₹{{ number_format($total, 2) }}
                </div>

                <button type="submit" class="btn btn-primary w-100 mt-3">Place Order</button>
            </form>
        </div>
    </div>
</div>
@endsection
