@extends('layouts.admin')

@section('title', 'Manage Payments')

@section('content')
<div class="container mt-4">
    <h2 class="mb-3">Manage Payments</h2>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>#ID</th>
                <th>Order ID</th>
                <th>User</th>
                <th>Amount</th>
                <th>Method</th>
                <th>Status</th>
                <th>Transaction ID</th>
                <th>Date</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($payments as $payment)
            <tr>
                <td>{{ $payment->id }}</td>
                <td>
                    <a href="{{ route('admin.orders.show', $payment->order_id) }}" class="text-primary">
                        #{{ $payment->order_id }}
                    </a>
                </td>
                <td>
                    {{ $payment->order && $payment->order->user ? $payment->order->user->name : 'N/A' }}
                </td>
                <td>₹{{ number_format($payment->amount, 2) }}</td>
                <td>{{ ucfirst($payment->method ?? 'N/A') }}</td>
                <td>
                    <span class="badge 
                        @if($payment->status === 'success') bg-success
                        @elseif($payment->status === 'pending') bg-warning
                        @elseif($payment->status === 'failed') bg-danger
                        @elseif($payment->status === 'refunded') bg-info
                        @else bg-secondary
                        @endif">
                        {{ ucfirst($payment->status) }}
                    </span>
                </td>
                <td>
                    <small>{{ $payment->transaction_id ?? 'N/A' }}</small>
                </td>
                <td>{{ $payment->created_at->format('d M Y, h:i A') }}</td>
                <td>
                    <a href="{{ route('admin.payments.show', $payment->id) }}" class="btn btn-sm btn-info">View</a>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="9" class="text-center py-3 text-muted">No payments found</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    {{-- Pagination --}}
    <div class="mt-4">
        {{ $payments->links('pagination::bootstrap-5') }}
    </div>

    {{-- Payments Count Info --}}
    <div class="mt-2 text-muted">
        <small>Showing {{ $payments->firstItem() ?? 0 }} to {{ $payments->lastItem() ?? 0 }} of {{ $payments->total() }} payments</small>
    </div>
</div>
@endsection

