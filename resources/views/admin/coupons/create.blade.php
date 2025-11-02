@extends('layouts.admin')

@section('title', 'Add Coupon')

@section('content')
<div class="container mt-4">
    <h2 class="mb-4">Add New Coupon</h2>

    <form action="{{ route('admin.coupons.store') }}" method="POST">
        @csrf
        @include('partials.messages')

        <div class="mb-3">
            <label>Coupon Code: <small class="text-muted">(Leave empty for auto-generation)</small></label>
            <input type="text" name="code" class="form-control" placeholder="e.g., SAVE20">
        </div>

        <div class="mb-3">
            <label>Discount Type:</label>
            <select name="type" class="form-select" required>
                <option value="percent">Percentage (%)</option>
                <option value="fixed">Fixed Amount (₹)</option>
            </select>
        </div>

        <div class="mb-3">
            <label>Discount Value:</label>
            <input type="number" step="0.01" name="value" class="form-control" placeholder="Enter discount amount" required>
            <small class="text-muted">Enter percentage (e.g., 10) or fixed amount (e.g., 100)</small>
        </div>

        <div class="mb-3">
            <label>Expiry Date:</label>
            <input type="datetime-local" name="expires_at" class="form-control">
            <small class="text-muted">Leave empty for no expiry</small>
        </div>

        <div class="mb-3">
            <div class="form-check">
                <input class="form-check-input" type="checkbox" name="is_active" value="1" id="is_active" checked>
                <label class="form-check-label" for="is_active">
                    Active
                </label>
            </div>
        </div>

        <button class="btn btn-success">Save Coupon</button>
        <a href="{{ route('admin.coupons.index') }}" class="btn btn-secondary">Cancel</a>
    </form>
</div>
@endsection
