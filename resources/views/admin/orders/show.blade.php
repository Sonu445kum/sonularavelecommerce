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
                    @if($order->status == 'pending') bg-warning 
                    @elseif($order->status == 'completed') bg-success 
                    @elseif($order->status == 'cancelled') bg-danger 
                    @else bg-secondary 
                    @endif">
                    {{ ucfirst($order->status) }}
                </span>
            </p>
            <p><strong>Payment Method:</strong> {{ ucfirst($order->payment_method) }}</p>
            <p><strong>Payment Status:</strong> {{ ucfirst($order->payment_status ?? 'N/A') }}</p>
            <p><strong>Order Date:</strong> {{ $order->created_at->format('d M Y, h:i A') }}</p>
        </div>
    </div>

    {{-- Shipping Address --}}
    <div class="card mb-4 shadow-sm">
        <div class="card-body">
            <h5 class="card-title">Shipping Address</h5>
            @if($order->shipping_address)
                <p>{{ $order->shipping_address }}</p>
            @else
                <p class="text-muted">No shipping address provided.</p>
            @endif
        </div>
    </div>

    {{-- Order Items Table --}}
    <div class="card mb-4 shadow-sm">
        <div class="card-body">
            <h5 class="card-title">Items in this Order</h5>

            <table class="table table-bordered">
                <thead>
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
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $item->product->name ?? 'Deleted Product' }}</td>
                            <td>{{ number_format($item->price, 2) }}</td>
                            <td>{{ $item->quantity }}</td>
                            <td>{{ number_format($item->price * $item->quantity, 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="text-end mt-3">
                <h5><strong>Subtotal:</strong> ₹{{ number_format($order->subtotal ?? 0, 2) }}</h5>
                <h5><strong>Discount:</strong> ₹{{ number_format($order->discount ?? 0, 2) }}</h5>
                <h4 class="text-success"><strong>Grand Total:</strong> ₹{{ number_format($order->total_amount, 2) }}</h4>
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
                            <option value="pending" {{ $order->status == 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="processing" {{ $order->status == 'processing' ? 'selected' : '' }}>Processing</option>
                            <option value="completed" {{ $order->status == 'completed' ? 'selected' : '' }}>Completed</option>
                            <option value="cancelled" {{ $order->status == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
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
