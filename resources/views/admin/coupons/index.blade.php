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
                <th>Discount (%)</th>
                <th>Expiry Date</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($coupons as $coupon)
            <tr>
                <td>{{ $coupon->id }}</td>
                <td>{{ $coupon->code }}</td>
                <td>{{ $coupon->discount }}</td>
                <td>{{ $coupon->expiry_date->format('d M Y') }}</td>
                <td>{{ $coupon->status ? 'Active' : 'Inactive' }}</td>
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
</div>
@endsection
