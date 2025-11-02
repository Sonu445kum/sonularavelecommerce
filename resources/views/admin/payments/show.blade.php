@extends('layouts.admin')

@section('title', 'Payment Details')

@section('content')
<div class="container mt-4">
    <h2 class="mb-4">Payment Details - #{{ $payment->id }}</h2>

    {{-- Payment Information --}}
    <div class="card mb-4 shadow-sm">
        <div class="card-body">
            <h5 class="card-title">Payment Information</h5>
            <p><strong>Payment ID:</strong> #{{ $payment->id }}</p>
            <p><strong>Order ID:</strong> 
                <a href="{{ route('admin.orders.show', $payment->order_id) }}" class="text-primary">
                    #{{ $payment->order_id }}
                </a>
            </p>
            <p><strong>User:</strong> {{ $payment->order && $payment->order->user ? $payment->order->user->name : 'N/A' }}</p>
            <p><strong>Amount:</strong> <strong class="text-success">₹{{ number_format($payment->amount, 2) }}</strong></p>
            <p><strong>Payment Method:</strong> {{ ucfirst($payment->method ?? 'N/A') }}</p>
            <p><strong>Status:</strong>
                <span class="badge 
                    @if($payment->status === 'success') bg-success
                    @elseif($payment->status === 'pending') bg-warning
                    @elseif($payment->status === 'failed') bg-danger
                    @elseif($payment->status === 'refunded') bg-info
                    @else bg-secondary
                    @endif">
                    {{ ucfirst($payment->status) }}
                </span>
            </p>
            <p><strong>Transaction ID:</strong> {{ $payment->transaction_id ?? 'N/A' }}</p>
            <p><strong>Payment Date:</strong> {{ $payment->created_at->format('d M Y, h:i A') }}</p>
        </div>
    </div>

    {{-- Order Information --}}
    @if($payment->order)
    <div class="card mb-4 shadow-sm">
        <div class="card-body">
            <h5 class="card-title">Related Order Information</h5>
            <p><strong>Order Total:</strong> ₹{{ number_format($payment->order->total, 2) }}</p>
            <p><strong>Order Status:</strong> 
                <span class="badge 
                    @if(in_array(strtolower($payment->order->status), ['delivered', 'completed'])) bg-success
                    @elseif(strtolower($payment->order->status) == 'cancelled') bg-danger
                    @else bg-warning
                    @endif">
                    {{ ucfirst($payment->order->status) }}
                </span>
            </p>
            <p><strong>Order Date:</strong> {{ $payment->order->created_at->format('d M Y, h:i A') }}</p>
        </div>
    </div>

    {{-- Order Items --}}
    @if($payment->order->orderItems && $payment->order->orderItems->count() > 0)
    <div class="card mb-4 shadow-sm">
        <div class="card-body">
            <h5 class="card-title">Order Items</h5>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Product</th>
                        <th>Price</th>
                        <th>Quantity</th>
                        <th>Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($payment->order->orderItems as $index => $item)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $item->product->title ?? $item->product->name ?? 'Deleted Product' }}</td>
                        <td>₹{{ number_format($item->price, 2) }}</td>
                        <td>{{ $item->quantity }}</td>
                        <td>₹{{ number_format($item->price * $item->quantity, 2) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif
    @endif

    {{-- Update Status Section --}}
    <div class="card shadow-sm">
        <div class="card-body">
            <h5 class="card-title">Update Payment Status</h5>
            <form action="{{ route('admin.payments.updateStatus', $payment->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="row align-items-center">
                    <div class="col-md-4">
                        <select name="status" class="form-select" required>
                            <option value="pending" {{ $payment->status === 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="success" {{ $payment->status === 'success' ? 'selected' : '' }}>Success</option>
                            <option value="failed" {{ $payment->status === 'failed' ? 'selected' : '' }}>Failed</option>
                            <option value="refunded" {{ $payment->status === 'refunded' ? 'selected' : '' }}>Refunded</option>
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

