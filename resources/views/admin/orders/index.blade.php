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
                <td>₹{{ $order->total_amount }}</td>
                <td>
                    <span class="badge 
                        @if($order->status == 'pending') bg-warning
                        @elseif($order->status == 'completed') bg-success
                        @else bg-secondary
                        @endif">
                        {{ ucfirst($order->status) }}
                    </span>
                </td>
                <td>{{ ucfirst($order->payment_method) }}</td>
                <td>{{ $order->created_at->format('d M Y') }}</td>
                <td>
                    <a href="{{ route('admin.orders.show', $order->id) }}" class="btn btn-sm btn-info">View</a>
                    <form action="{{ route('admin.orders.destroy', $order->id) }}" method="POST" style="display:inline;">
                        @csrf @method('DELETE')
                        <button class="btn btn-sm btn-danger" onclick="return confirm('Delete this order?')">Delete</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
