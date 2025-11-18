@extends('layouts.app')

@section('title', 'Shipping Policy')

@section('content')
<div class="max-w-5xl mx-auto px-4 py-12">

    <div class="text-center mb-8">
        <h1 class="text-4xl font-bold text-gray-800">Shipping <span class="text-blue-600">Policy</span></h1>
        <p class="text-gray-600 text-lg mt-2">
            Learn how we deliver your products safely and on time.
        </p>
    </div>

    <div class="bg-white p-8 rounded-2xl shadow-md space-y-7 text-gray-700">

        <div>
            <h2 class="text-2xl font-semibold mb-2">1. Delivery Time</h2>
            <p>
                Orders are shipped within 24–48 hours and delivered within <strong>3–7 business days</strong> 
                depending on your location.
            </p>
        </div>

        <div>
            <h2 class="text-2xl font-semibold mb-2">2. Shipping Charges</h2>
            <p>
                A flat rate of ₹50 is applied to all orders. Shipping is free for orders above ₹999.
            </p>
        </div>

        <div>
            <h2 class="text-2xl font-semibold mb-2">3. Order Tracking</h2>
            <p>
                Once shipped, a tracking link will be sent to your email or phone to track your package in real-time.
            </p>
        </div>

        <div>
            <h2 class="text-2xl font-semibold mb-2">4. Delivery Partners</h2>
            <p>
                We work with trusted courier companies to ensure safe and timely delivery.
            </p>
        </div>

        <div>
            <h2 class="text-2xl font-semibold mb-2">5. Delays</h2>
            <p>
                Unexpected delays may occur due to weather, holidays, or high-demand seasons. 
                We ensure your order is delivered at the earliest.
            </p>
        </div>

    </div>

</div>
@endsection
