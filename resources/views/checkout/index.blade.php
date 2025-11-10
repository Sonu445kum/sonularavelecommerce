@extends('layouts.app')

@section('title', 'Checkout')

@section('content')
<div class="container my-5">

    <h2 class="mb-4 fw-bold">Checkout</h2>

    {{-- ❌ Show errors --}}
    @if($errors->any())
        <div class="alert alert-danger">
            @foreach($errors->all() as $error) <div>{{ $error }}</div> @endforeach
        </div>
    @endif

    {{-- ✅ Success message --}}
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="row g-4">

       {{-- 📦 Cart Items --}}
<div class="col-lg-7">
    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-header bg-light fw-bold fs-5">Your Cart</div>
        <div class="card-body">

            @if(isset($cartItems) && $cartItems->isNotEmpty())
                @foreach ($cartItems as $item)
                    @php
                        $product = $item->product ?? null;
                        $image = $product && $product->images && $product->images->first()
                            ? $product->images->first()->path
                            : 'images/no-image.png';
                    @endphp

                    <div class="d-flex align-items-center mb-3 border-bottom pb-2">
                        <img src="{{ asset($image) }}" 
                             class="rounded-3 me-3" 
                             style="width: 70px; height: 70px; object-fit: cover;" 
                             alt="{{ $product->title ?? 'Product' }}">
                        <div class="flex-grow-1">
                            <h6 class="fw-semibold mb-1">{{ $product->title ?? 'Product Name' }}</h6>
                            <p class="text-muted mb-1">Qty: {{ $item->quantity ?? 1 }}</p>
                            <p class="fw-bold mb-0">₹{{ number_format(($item->price ?? 0) * ($item->quantity ?? 1), 2) }}</p>
                        </div>
                    </div>
                @endforeach

                <div class="text-end fw-bold fs-5 mt-3">
                    Subtotal: ₹{{ number_format($cartItems->sum(fn($it) => ($it->price ?? 0) * ($it->quantity ?? 1)), 2) }}
                </div>
            @else
                <p class="text-muted text-center mb-0">Your cart is empty.</p>
            @endif

        </div>
    </div>
</div>

        {{-- 🏠 Shipping & Payment --}}
        <div class="col-lg-5">
            <form action="{{ route('checkout.process') }}" method="POST" class="card border-0 shadow-sm rounded-4 p-4">
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

                <button type="submit" class="btn btn-primary w-100 mt-3">Place Order</button>
            </form>
        </div>
    </div>
</div>
@endsection
