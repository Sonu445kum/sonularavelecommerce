@extends('layouts.app')

@section('title', 'Order Details')

@section('content')
<div class="container my-5">

    {{-- 🧭 Breadcrumb --}}
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ route('orders.index') }}">My Orders</a></li>
            <li class="breadcrumb-item active" aria-current="page">Order #{{ $order->id }}</li>
        </ol>
    </nav>

    {{-- 🧾 Order Summary --}}
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body d-flex justify-content-between align-items-center flex-wrap">
            <div>
                <h4 class="fw-bold mb-1">Order #{{ $order->id }}</h4>
                <p class="mb-0 text-muted">Placed on {{ $order->created_at->format('d M Y, h:i A') }}</p>
            </div>
            <span class="badge bg-{{ $order->status === 'delivered' ? 'success' : ($order->status === 'pending' ? 'warning' : 'secondary') }} fs-6">
                {{ ucfirst($order->status) }}
            </span>
        </div>
    </div>

    <div class="row g-4">

        {{-- 📦 Ordered Items --}}
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header bg-light fw-bold fs-5">Ordered Items</div>
                <div class="card-body">

                    @foreach ($order->orderItems as $item)
                        @php
                            $product = $item->product;
                            $image = $product->productImages->first()->image_path ?? 'images/no-image.png';
                        @endphp

                        <div class="d-flex align-items-center mb-4 border-bottom pb-3">
                            <img src="{{ asset($image) }}" class="rounded-3 me-3"
                                 style="width: 90px; height: 90px; object-fit: cover;" alt="{{ $product->title }}">
                            <div class="flex-grow-1">
                                <h6 class="fw-semibold mb-1">{{ $product->title }}</h6>
                                <p class="text-muted mb-1">Qty: {{ $item->quantity }}</p>
                                <p class="fw-bold mb-0">₹{{ number_format($item->price * $item->quantity, 2) }}</p>
                            </div>
                        </div>
                    @endforeach

                    <div class="text-end fw-bold fs-5 mt-3">
                        Total: ₹{{ number_format($order->total_amount, 2) }}
                    </div>
                </div>
            </div>
        </div>

        {{-- 📬 Shipping & Payment Info --}}
        <div class="col-lg-4">
            <div class="row g-3">

                {{-- 🏠 Shipping Address --}}
                <div class="col-md-12">
                    <div class="card border-0 shadow-sm rounded-4 h-100">
                        <div class="card-header bg-light fw-bold fs-5">Shipping Address</div>
                        <div class="card-body">
                            @php
                                $shipping = is_array($order->shipping_address)
                                    ? (object) $order->shipping_address
                                    : (is_string($order->shipping_address)
                                        ? json_decode($order->shipping_address)
                                        : $order->shipping_address);
                            @endphp

                            <p class="fw-semibold mb-1">{{ $shipping->name ?? $order->user->name ?? 'N/A' }}</p>
                            <p class="mb-1">Email: {{ $shipping->email ?? $order->user->email ?? 'N/A' }}</p>
                            <p class="mb-1">Phone: {{ $shipping->phone ?? 'N/A' }}</p>
                            <p class="mb-1">Pincode: {{ $shipping->pincode ?? 'N/A' }}</p>
                            <p class="mb-0">Address: {{ $shipping->address ?? 'N/A' }}</p>
                        </div>
                    </div>
                </div>

                {{-- 💳 Payment Summary --}}
                <div class="col-md-12">
                    <div class="card border-0 shadow-sm rounded-4 h-100">
                        <div class="card-header bg-light fw-bold fs-5">Payment Summary</div>
                        <div class="card-body">
                            @php
                                $payment = $order->payments->first();
                            @endphp

                            @if($payment)
                                <p class="mb-1">Payment ID: <span class="fw-semibold">{{ $payment->payment_id ?? 'N/A' }}</span></p>
                                <p class="mb-1">Mode: <span class="fw-semibold text-capitalize">{{ $payment->payment_method ?? 'N/A' }}</span></p>
                                <p class="mb-1">Status:
                                    <span class="badge bg-{{ $payment->status === 'paid' ? 'success' : 'danger' }}">
                                        {{ ucfirst($payment->status) }}
                                    </span>
                                </p>
                                <p class="mb-0">Paid On: {{ optional($payment->created_at)->format('d M Y, h:i A') }}</p>
                            @else
                                <p class="text-muted mb-0">No payment record found.</p>
                            @endif
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
@endsection
