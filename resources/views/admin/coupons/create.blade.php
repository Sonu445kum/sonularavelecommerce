@extends('layouts.admin')

@section('title', 'Add Coupon')

@section('content')
<div class="container mt-4">
    <h2 class="mb-4">Add New Coupon</h2>

    <form action="{{ route('admin.coupons.store') }}" method="POST">
        @csrf
        @include('partials.messages')

        <div class="mb-3">
            <label>Coupon Code:</label>
            <input type="text" name="code" class="form-control" required>
        </div>

        <div class="mb-3">
            <label>Discount (%):</label>
            <input type="number" name="discount" class="form-control" required>
        </div>

        <div class="mb-3">
            <label>Expiry Date:</label>
            <input type="date" name="expiry_date" class="form-control" required>
        </div>

        <button class="btn btn-success">Save Coupon</button>
    </form>
</div>
@endsection
