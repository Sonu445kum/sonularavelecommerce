@extends('layouts.admin')

@section('title', 'Edit Coupon')

@section('content')
<div class="container mt-4">
    <h2 class="mb-4">Edit Coupon</h2>

    <form action="{{ route('admin.coupons.update', $coupon->id) }}" method="POST">
        @csrf @method('PUT')
        @include('partials.messages')

        <div class="mb-3">
            <label>Coupon Code:</label>
            <input type="text" name="code" class="form-control" value="{{ $coupon->code }}" required>
        </div>

        <div class="mb-3">
            <label>Discount (%):</label>
            <input type="number" name="discount" class="form-control" value="{{ $coupon->discount }}" required>
        </div>

        <div class="mb-3">
            <label>Expiry Date:</label>
            <input type="date" name="expiry_date" class="form-control" value="{{ $coupon->expiry_date->format('Y-m-d') }}" required>
        </div>

        <button class="btn btn-primary">Update Coupon</button>
    </form>
</div>
@endsection
