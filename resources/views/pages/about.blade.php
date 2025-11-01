@extends('layouts.app')

@section('title', 'About Us')

@section('content')
<div class="max-w-6xl mx-auto px-4 py-12">
    <div class="text-center mb-10">
        <h1 class="text-4xl font-bold text-gray-800 mb-4">About <span class="text-blue-600">MyShop</span></h1>
        <p class="text-gray-600 text-lg">Your one-stop destination for the best online shopping experience.</p>
    </div>

    <div class="grid md:grid-cols-2 gap-10 items-center">
        <div>
            <img src="https://cdn.dribbble.com/users/285475/screenshots/2083086/dribbble_1.gif" 
                 alt="About Us" 
                 class="rounded-2xl shadow-md hover:scale-105 transition-transform duration-300">
        </div>

        <div>
            <h2 class="text-2xl font-semibold text-gray-800 mb-3">Who We Are</h2>
            <p class="text-gray-600 mb-4">
                MyShop is an innovative eCommerce platform designed to bring quality products closer to you.
                From fashion and electronics to home essentials, we aim to provide top-notch products at the best prices.
            </p>
            <p class="text-gray-600">
                Our mission is to deliver trust, quality, and convenience to our customers. We’re constantly working to
                expand our range and improve your shopping experience with advanced technologies and seamless design.
            </p>
        </div>
    </div>

    <div class="mt-16 text-center">
        <h2 class="text-2xl font-semibold text-gray-800 mb-3">Why Choose Us?</h2>
        <div class="grid sm:grid-cols-2 md:grid-cols-3 gap-8 mt-6">
            <div class="p-6 bg-white rounded-xl shadow-md hover:shadow-lg transition">
                <h3 class="text-lg font-semibold text-blue-600 mb-2">Fast Delivery</h3>
                <p class="text-gray-600 text-sm">Get your orders delivered faster than ever with our trusted partners.</p>
            </div>

            <div class="p-6 bg-white rounded-xl shadow-md hover:shadow-lg transition">
                <h3 class="text-lg font-semibold text-blue-600 mb-2">Secure Payments</h3>
                <p class="text-gray-600 text-sm">All transactions are safe and encrypted for your peace of mind.</p>
            </div>

            <div class="p-6 bg-white rounded-xl shadow-md hover:shadow-lg transition">
                <h3 class="text-lg font-semibold text-blue-600 mb-2">24/7 Support</h3>
                <p class="text-gray-600 text-sm">Our team is always here to assist you, anytime, anywhere.</p>
            </div>
        </div>
    </div>
</div>
@endsection
