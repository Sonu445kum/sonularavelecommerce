@extends('layouts.admin')

@section('title', 'Manage Orders')

@section('content')
<div class="container mt-4">
    <h2 class="mb-3">Manage Orders</h2>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>#Order ID</th>
                <th>User</th>
                <th>Total Amount</th>
                <th>Status</th>
                <th>Payment Method</th>
                <th>Date</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($orders as $order)
            <tr>
                <td>#{{ $order->id }}</td>
                <td>{{ $order->user->name ?? 'Guest' }}</td>
                <td>₹{{ number_format($order->total, 2) }}</td>
                <td>
                    <span class="badge 
                        @if(strtolower($order->status) == 'pending') bg-warning
                        @elseif(in_array(strtolower($order->status), ['completed', 'delivered'])) bg-success
                        @else bg-secondary
                        @endif">
                        {{ ucfirst($order->status) }}
                    </span>
                </td>
                <td>{{ $order->latestPayment->method ?? 'N/A' }}</td>
                <td>{{ $order->created_at->format('d M Y') }}</td>
                <td>
                    <a href="{{ route('admin.orders.show', $order->id) }}" class="btn btn-sm btn-info">View</a>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    {{-- Pagination --}}
    <div class="mt-4">
        {{ $orders->links('pagination::bootstrap-5') }}
    </div>

    {{-- Orders Count Info --}}
    <div class="mt-2 text-muted">
        <small>Showing {{ $orders->firstItem() ?? 0 }} to {{ $orders->lastItem() ?? 0 }} of {{ $orders->total() }} orders</small>
    </div>
</div>
@endsection
