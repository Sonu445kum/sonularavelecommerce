@extends('layouts.admin')

@section('title', 'Manage Payments')

@section('content')
<div class="container mt-4">

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="mb-0">Manage Payments</h2>
    </div>

    <div class="card shadow-sm">
        <div class="card-body p-0">

            <table class="table table-hover table-striped align-middle mb-0">
                <thead class="table-dark">
                    <tr>
                        <th>#ID</th>
                        <th>Order</th>
                        <th>User</th>
                        <th>Amount</th>
                        <th>Method</th>
                        <th>Status</th>
                        <th>Transaction ID</th>
                        <th>Date</th>
                        <th class="text-center">Actions</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($payments as $payment)
                    <tr>
                        <td>{{ $payment->id }}</td>

                        {{-- Order ID --}}
                        <td>
                            @if($payment->order)
                                <a href="{{ route('admin.orders.show', $payment->order->id) }}"
                                    class="text-decoration-none text-primary fw-semibold">
                                    #{{ $payment->order->id }}
                                </a>
                            @else
                                <span class="text-muted">N/A</span>
                            @endif
                        </td>

                        {{-- User --}}
                        <td>
                            @if($payment->order && $payment->order->user)
                                {{ $payment->order->user->name }}
                                <br>
                                <small class="text-muted">{{ $payment->order->user->email }}</small>
                            @else
                                <span class="text-muted">Unknown User</span>
                            @endif
                        </td>

                        {{-- Amount --}}
                        <td class="fw-bold">₹{{ number_format($payment->amount, 2) }}</td>

                        {{-- Method --}}
                        <td>{{ ucfirst($payment->method ?? 'N/A') }}</td>

                        {{-- Status Badge --}}
                        <td>
                            @php
                                $statusColors = [
                                    'success' => 'success',
                                    'pending' => 'warning',
                                    'failed'  => 'danger',
                                    'refunded'=> 'info'
                                ];
                                $color = $statusColors[$payment->status] ?? 'secondary';
                            @endphp

                            <span class="badge bg-{{ $color }} px-3 py-2">
                                {{ ucfirst($payment->status) }}
                            </span>
                        </td>

                        {{-- Transaction ID --}}
                        <td>
                            <small class="text-break d-block" style="max-width: 150px;">
                                {{ $payment->transaction_id ?? 'N/A' }}
                            </small>
                        </td>

                        {{-- Date --}}
                        <td>
                            <span>{{ $payment->created_at->format('d M Y') }}</span><br>
                            <small class="text-muted">{{ $payment->created_at->format('h:i A') }}</small>
                        </td>

                        {{-- Actions --}}
                        <td class="text-center">
                            <a href="{{ route('admin.payments.show', $payment->id) }}"
                               class="btn btn-sm btn-outline-primary">
                                View
                            </a>
                        </td>
                    </tr>

                    @empty
                    <tr>
                        <td colspan="9" class="text-center py-4">
                            <img src="https://cdn-icons-png.flaticon.com/512/4076/4076549.png"
                                 width="70" class="mb-2">
                            <p class="text-muted mb-0">No payments found</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>

        </div>
    </div>

    {{-- Pagination --}}
    <div class="mt-3">
        {{ $payments->links('pagination::bootstrap-5') }}
    </div>

    {{-- Summary --}}
    <div class="mt-2 text-muted">
        <small>
            Showing {{ $payments->firstItem() ?? 0 }} to {{ $payments->lastItem() ?? 0 }}
            of {{ $payments->total() }} payments
        </small>
    </div>

</div>
@endsection
