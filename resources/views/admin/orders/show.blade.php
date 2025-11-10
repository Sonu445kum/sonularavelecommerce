@extends('layouts.admin')

@section('title', 'Order Details')

@section('content')
<div class="container mt-4">
    <h2 class="mb-4">Order Details - #{{ $order->id }}</h2>

    {{-- Basic Order Information --}}
    <div class="card mb-4 shadow-sm">
        <div class="card-body">
            <h5 class="card-title">Order Information</h5>
            <p><strong>Order ID:</strong> #{{ $order->id }}</p>
            <p><strong>User:</strong> {{ $order->user->name ?? 'Guest User' }} ({{ $order->user->email ?? 'N/A' }})</p>
            <p><strong>Status:</strong>
                <span class="badge 
                    @if(strtolower($order->status) == 'pending') bg-warning 
                    @elseif(in_array(strtolower($order->status), ['delivered', 'completed'])) bg-success 
                    @elseif(strtolower($order->status) == 'cancelled') bg-danger 
                    @elseif(strtolower($order->status) == 'refunded') bg-info 
                    @else bg-secondary 
                    @endif">
                    {{ ucfirst($order->status) }}
                </span>
            </p>
            <p><strong>Payment Method:</strong> {{ ucfirst($order->payment_method ?? 'N/A') }}</p>
            <p><strong>Payment Status:</strong> {{ ucfirst($order->payment_status ?? 'N/A') }}</p>
            <p><strong>Order Date:</strong> {{ $order->created_at->format('d M Y, h:i A') }}</p>
        </div>
    </div>

  @php
    $shipping = $order->shipping_info;
@endphp

@if($shipping)
    <p>
        <strong>{{ $shipping->name ?? 'N/A' }}</strong><br>
        {{ $shipping->address_line ?? '' }}<br>
        {{ $shipping->city ?? '' }} - {{ $shipping->postalcode ?? '' }}<br>
        <strong>Phone:</strong> {{ $shipping->phone ?? 'N/A' }}<br>
        <strong>Label:</strong> {{ $shipping->label ?? 'N/A' }}
    </p>
@else
    <p class="text-muted">No shipping address provided.</p>
@endif





    {{-- Order Items Table --}}
    <div class="card mb-4 shadow-sm">
        <div class="card-body">
            <h5 class="card-title">Items in this Order</h5>

            <table class="table table-bordered table-striped">
                <thead class="table-dark">
                    <tr>
                        <th>#</th>
                        <th>Product</th>
                        <th>Price (₹)</th>
                        <th>Quantity</th>
                        <th>Subtotal (₹)</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($order->orderItems as $index => $item)
                        @php 
                            $itemPrice = $item->price ?? ($item->product->price ?? 0);
                        @endphp
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $item->product->title ?? $item->product->name ?? 'Deleted Product' }}</td>
                            <td>₹{{ number_format($itemPrice, 2) }}</td>
                            <td>{{ $item->quantity }}</td>
                            <td>₹{{ number_format($item->calculated_subtotal ?? ($itemPrice * $item->quantity), 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            {{-- Totals --}}
            <div class="text-end mt-3">
                <h5><strong>Subtotal:</strong> ₹{{ number_format($order->calculated_subtotal ?? 0, 2) }}</h5>
                <h5><strong>Shipping:</strong> ₹{{ number_format($order->calculated_shipping ?? 0, 2) }}</h5>
                <h5><strong>Tax:</strong> ₹{{ number_format($order->calculated_tax ?? 0, 2) }}</h5>
                <h5><strong>Discount:</strong> -₹{{ number_format($order->calculated_discount ?? 0, 2) }}</h5>
                <h4 class="text-success"><strong>Grand Total:</strong> ₹{{ number_format($order->calculated_total ?? 0, 2) }}</h4>
            </div>
        </div>
    </div>

    {{-- Update Status Section --}}
    <div class="card shadow-sm">
        <div class="card-body">
            <h5 class="card-title">Update Order Status</h5>
            <form action="{{ route('admin.orders.updateStatus', $order->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="row align-items-center">
                    <div class="col-md-4">
                        <select name="status" class="form-select" required>
                            @foreach(['pending','processing','shipped','delivered','cancelled','refunded'] as $status)
                                <option value="{{ $status }}" {{ strtolower($order->status) == $status ? 'selected' : '' }}>
                                    {{ ucfirst($status) }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <button class="btn btn-primary">Update Status</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
