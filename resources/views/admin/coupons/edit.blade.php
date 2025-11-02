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
            <input type="text" name="code" class="form-control" value="{{ $coupon->code }}" readonly>
            <small class="text-muted">Coupon code cannot be changed</small>
        </div>

        <div class="mb-3">
            <label>Discount Type:</label>
            <select name="type" class="form-select" required>
                <option value="percent" {{ $coupon->type === 'percent' ? 'selected' : '' }}>Percentage (%)</option>
                <option value="fixed" {{ $coupon->type === 'fixed' ? 'selected' : '' }}>Fixed Amount (₹)</option>
            </select>
        </div>

        <div class="mb-3">
            <label>Discount Value:</label>
            <input type="number" step="0.01" name="value" class="form-control" value="{{ $coupon->value }}" required>
            <small class="text-muted">Percentage (e.g., 10) or fixed amount (e.g., 100)</small>
        </div>

        <div class="mb-3">
            <label>Expiry Date:</label>
            <input type="datetime-local" name="expires_at" class="form-control" 
                   value="{{ $coupon->expires_at ? $coupon->expires_at->format('Y-m-d\TH:i') : '' }}">
            <small class="text-muted">Leave empty for no expiry</small>
        </div>

        <div class="mb-3">
            <div class="form-check">
                <input class="form-check-input" type="checkbox" name="is_active" value="1" id="is_active" {{ $coupon->is_active ? 'checked' : '' }}>
                <label class="form-check-label" for="is_active">
                    Active
                </label>
            </div>
        </div>

        <button class="btn btn-primary">Update Coupon</button>
        <a href="{{ route('admin.coupons.index') }}" class="btn btn-secondary">Cancel</a>
    </form>
</div>
@endsection
