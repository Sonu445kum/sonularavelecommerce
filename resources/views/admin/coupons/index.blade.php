@extends('layouts.admin')

@section('title', 'Manage Coupons')

@section('content')
<div class="container mt-4">
    <h2 class="mb-3">Manage Coupons</h2>
    <a href="{{ route('admin.coupons.create') }}" class="btn btn-primary mb-3">Add New Coupon</a>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>#ID</th>
                <th>Code</th>
                <th>Discount</th>
                <th>Expiry Date</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($coupons as $coupon)
            <tr>
                <td>{{ $coupon->id }}</td>
                <td><strong>{{ $coupon->code }}</strong></td>
                <td>
                    @if($coupon->type === 'percent')
                        {{ $coupon->value }}%
                    @else
                        ₹{{ number_format($coupon->value, 2) }}
                    @endif
                </td>
                <td>{{ $coupon->expires_at ? $coupon->expires_at->format('d M Y') : 'No expiry' }}</td>
                <td>
                    <span class="badge {{ $coupon->is_active ? 'bg-success' : 'bg-secondary' }}">
                        {{ $coupon->is_active ? 'Active' : 'Inactive' }}
                    </span>
                </td>
                <td>
                    <a href="{{ route('admin.coupons.edit', $coupon->id) }}" class="btn btn-sm btn-warning">Edit</a>
                    <form action="{{ route('admin.coupons.destroy', $coupon->id) }}" method="POST" style="display:inline;">
                        @csrf @method('DELETE')
                        <button class="btn btn-sm btn-danger" onclick="return confirm('Delete this coupon?')">Delete</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    {{-- Pagination --}}
    <div class="mt-4">
        {{ $coupons->links('pagination::bootstrap-5') }}
    </div>

    {{-- Coupons Count Info --}}
    <div class="mt-2 text-muted">
        <small>Showing {{ $coupons->firstItem() ?? 0 }} to {{ $coupons->lastItem() ?? 0 }} of {{ $coupons->total() }} coupons</small>
    </div>
</div>
@endsection
