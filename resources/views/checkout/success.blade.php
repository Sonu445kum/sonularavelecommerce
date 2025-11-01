
@extends('layouts.app')

@section('title', 'Payment Success')

@section('content')
<div class="container text-center py-5">
    <h1 class="text-success">✅ Payment Successful</h1>
    <p>Your order has been successfully placed!</p>
    <h5>Order ID: {{ $order->id ?? 'N/A' }}</h5>
    <a href="{{ route('orders.index') }}" class="btn btn-outline-primary mt-4">View My Orders</a>
</div>
@endsection

