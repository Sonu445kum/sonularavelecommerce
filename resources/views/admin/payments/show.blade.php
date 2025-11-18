@extends('layouts.admin')

@section('title', 'Payment Details')

@section('content')
<div class="container mt-4">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold">Payment Details — #{{ $payment->id }}</h2>
        <a href="{{ route('admin.payments.index') }}" class="btn btn-secondary btn-sm">
            ← Back to Payments
        </a>
    </div>

    {{-- =======================
        PAYMENT INFO
    ======================== --}}
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-dark text-white">
            <h5 class="mb-0">Payment Information</h5>
        </div>
        <div class="card-body">

            <div class="row mb-3">
                <div class="col-md-4">
                    <strong>Payment ID:</strong><br>
                    #{{ $payment->id }}
                </div>

                <div class="col-md-4">
                    <strong>Order ID:</strong><br>
                    @if($payment->order)
                        <a href="{{ route('admin.orders.show', $payment->order->id) }}"
                           class="text-decoration-none text-primary fw-semibold">
                           #{{ $payment->order->id }}
                        </a>
                    @else
                        <span class="text-muted">N/A</span>
                    @endif
                </div>

                <div class="col-md-4">
                    <strong>User:</strong><br>
                    @if($payment->order && $payment->order->user)
                        {{ $payment->order->user->name }}  
                        <br><small class="text-muted">{{ $payment->order->user->email }}</small>
                    @else
                        <span class="text-muted">Unknown User</span>
                    @endif
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-4">
                    <strong>Amount:</strong><br>
                    <span class="text-success fw-bold">
                        ₹{{ number_format($payment->amount, 2) }}
                    </span>
                </div>

                <div class="col-md-4">
                    <strong>Method:</strong><br>
                    {{ ucfirst($payment->method ?? 'N/A') }}
                </div>

                <div class="col-md-4">
                    <strong>Status:</strong><br>
                    @php
                        $statusColors = [
                            'success' => 'success',
                            'pending' => 'warning',
                            'failed'  => 'danger',
                            'refunded'=> 'info'
                        ];
                        $color = $statusColors[$payment->status] ?? 'secondary';
                    @endphp
                    <span class="badge bg-{{ $color }} px-3 py-2">{{ ucfirst($payment->status) }}</span>
                </div>
            </div>

            <div class="row mb-2">
                <div class="col-md-4">
                    <strong>Transaction ID:</strong><br>
                    <span class="text-break">{{ $payment->transaction_id ?? 'N/A' }}</span>
                </div>

                <div class="col-md-4">
                    <strong>Payment Date:</strong><br>
                    {{ $payment->created_at->format('d M Y, h:i A') }}
                </div>
            </div>

        </div>
    </div>

    {{-- =======================
        ORDER INFO
    ======================== --}}
    @if($payment->order)
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-dark text-white">
            <h5 class="mb-0">Order Information</h5>
        </div>
        <div class="card-body">

            <div class="row mb-3">
                <div class="col-md-4">
                    <strong>Order Total:</strong><br>
                    ₹{{ number_format($payment->order->total, 2) }}
                </div>

                <div class="col-md-4">
                    <strong>Status:</strong><br>
                    @php
                        $orderStatus = strtolower($payment->order->status);
                        $badge = ($orderStatus == 'cancelled') ? 'danger' :
                                 (in_array($orderStatus, ['delivered', 'completed']) ? 'success' : 'warning');
                    @endphp
                    <span class="badge bg-{{ $badge }} px-3 py-2">
                        {{ ucfirst($payment->order->status) }}
                    </span>
                </div>

                <div class="col-md-4">
                    <strong>Order Date:</strong><br>
                    {{ $payment->order->created_at->format('d M Y, h:i A') }}
                </div>
            </div>

        </div>
    </div>

    {{-- =======================
        ORDER ITEMS
    ======================== --}}
    @if($payment->order->orderItems->count() > 0)
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-dark text-white">
            <h5 class="mb-0">Order Items</h5>
        </div>
        <div class="card-body p-0">
            <table class="table table-striped table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Product</th>
                        <th>Price (₹)</th>
                        <th>Qty</th>
                        <th>Subtotal (₹)</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($payment->order->orderItems as $i => $item)
                    <tr>
                        <td>{{ $i + 1 }}</td>
                        <td>
                            {{ $item->product->title ?? $item->product->name ?? 'Deleted Product' }}
                        </td>
                        <td>{{ number_format($item->price, 2) }}</td>
                        <td>{{ $item->quantity }}</td>
                        <td>{{ number_format($item->price * $item->quantity, 2) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif
    @endif

    {{-- =======================
        UPDATE STATUS FORM
    ======================== --}}
    <div class="card shadow-sm mb-5">
        <div class="card-header bg-dark text-white">
            <h5 class="mb-0">Update Payment Status</h5>
        </div>
        <div class="card-body">

            <form action="{{ route('admin.payments.updateStatus', $payment->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label fw-bold">Select Status</label>
                        <select name="status" class="form-select" required>
                            <option value="pending"  {{ $payment->status == 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="success"  {{ $payment->status == 'success' ? 'selected' : '' }}>Success</option>
                            <option value="failed"   {{ $payment->status == 'failed' ? 'selected' : '' }}>Failed</option>
                            <option value="refunded" {{ $payment->status == 'refunded' ? 'selected' : '' }}>Refunded</option>
                        </select>
                    </div>

                    <div class="col-md-4 d-flex align-items-end">
                        <button class="btn btn-primary w-100">Update Status</button>
                    </div>
                </div>

            </form>

        </div>
    </div>

</div>
@endsection
