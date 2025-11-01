@extends('layouts.app')

@section('title', 'Payment Cancelled')

@section('content')
<div class="container text-center py-5">
    <h1 class="text-danger">❌ Payment Cancelled</h1>
    <p>Your payment was cancelled. Please try again.</p>
    <a href="{{ route('checkout.index') }}" class="btn btn-outline-danger mt-4">Back to Checkout</a>
</div>
@endsection
