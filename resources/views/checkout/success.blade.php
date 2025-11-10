@extends('layouts.app')

@section('title', 'Order Placed')

@section('content')
<div class="container my-5">

    <div class="alert alert-success mb-4">
        <h4 class="fw-bold">✅ Order #{{ $order->id }} Placed Successfully!</h4>
        <p>Thank you for shopping with us.</p>
    </div>

    <div class="row g-4">

        {{-- 📦 Ordered Items --}}
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header bg-light fw-bold fs-5">Ordered Items</div>
                <div class="card-body">
                    @foreach($order->items as $item)
                        @php
                            $product = $item->product;
                            $image = $item->product_image ?? 'images/no-image.png';
                        @endphp

                        <div class="d-flex align-items-center mb-3 border-bottom pb-2">
                            <img src="{{ asset($image) }}" class="rounded-3 me-3" style="width: 70px; height: 70px; object-fit: cover;" alt="{{ $product->title ?? $item->product_name }}">
                            <div class="flex-grow-1">
                                <h6 class="fw-semibold mb-1">{{ $item->product_name }}</h6>
                                <p class="text-muted mb-1">Qty: {{ $item->quantity }}</p>
                                <p class="fw-bold mb-0">₹{{ number_format($item->total_price, 2) }}</p>
                            </div>
                        </div>
                    @endforeach

                    <div class="text-end fw-bold fs-5 mt-3">
                        Total Paid: ₹{{ number_format($order->total, 2) }}
                    </div>
                </div>
            </div>
        </div>

        {{-- 🏠 Shipping & Payment --}}
        <div class="col-lg-4">
            <div class="row g-3">

                {{-- Shipping Address --}}
                <div class="col-md-12">
                    <div class="card border-0 shadow-sm rounded-4 h-100">
                        <div class="card-header bg-light fw-bold fs-5">Shipping Address</div>
                        <div class="card-body">
                            @php
                                $address = $order->address;
                            @endphp
                            <p class="fw-semibold mb-1">{{ $address->name ?? $order->user->name ?? 'N/A' }}</p>
                            <p class="mb-1">Email: {{ $order->user->email ?? 'N/A' }}</p>
                            <p class="mb-1">Phone: {{ $address->phone ?? 'N/A' }}</p>
                            <p class="mb-1">Pincode: {{ $address->postal_code ?? 'N/A' }}</p>
                            <p class="mb-0">Address: {{ $address->address_line1 ?? 'N/A' }}</p>
                        </div>
                    </div>
                </div>

                {{-- Payment Summary --}}
                <div class="col-md-12">
                    <div class="card border-0 shadow-sm rounded-4 h-100">
                        <div class="card-header bg-light fw-bold fs-5">Payment Summary</div>
                        <div class="card-body">
                            @php
                                $payment = $order->payments->first();
                            @endphp

                            @if($payment)
                                <p class="mb-1">Payment ID: <span class="fw-semibold">{{ $payment->transaction_id ?? 'N/A' }}</span></p>
                                <p class="mb-1">Method: <span class="fw-semibold text-capitalize">{{ $payment->method ?? 'N/A' }}</span></p>
                                <p class="mb-1">Status:
                                    <span class="badge bg-{{ $payment->status === 'success' ? 'success' : 'danger' }}">
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
