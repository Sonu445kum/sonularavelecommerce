@extends('layouts.app')

@section('title', 'Terms & Conditions')

@section('content')
<div class="max-w-5xl mx-auto px-4 py-12">

    <div class="text-center mb-10">
        <h1 class="text-4xl font-bold text-gray-800">
            Terms & <span class="text-blue-600">Conditions</span>
        </h1>
        <p class="text-gray-600 mt-2 text-lg">
            Please read the terms carefully before using our services.
        </p>
    </div>

    <div class="bg-white p-8 rounded-2xl shadow-md space-y-8 text-gray-700 leading-relaxed">

        <!-- 1. Introduction -->
        <div>
            <h2 class="text-2xl font-semibold mb-2">1. Introduction</h2>
            <p>
                Welcome to <strong>MyShop</strong>. By accessing or using our website, mobile app, or services, 
                you agree to be bound by these Terms & Conditions. If you do not agree, please do not use our platform.
            </p>
        </div>

        <!-- 2. User Responsibilities -->
        <div>
            <h2 class="text-2xl font-semibold mb-2">2. User Responsibilities</h2>
            <p>
                You agree to use our platform only for lawful purposes. You must not:
            </p>
            <ul class="list-disc pl-6 mt-2 space-y-1">
                <li>Provide false information during registration.</li>
                <li>Attempt to disrupt website functionality.</li>
                <li>Use automated tools to scrape our content.</li>
                <li>Engage in fraudulent transactions.</li>
            </ul>
        </div>

        <!-- 3. Product Information -->
        <div>
            <h2 class="text-2xl font-semibold mb-2">3. Product Information</h2>
            <p>
                We strive to ensure product descriptions, prices, and images are accurate. 
                However, minor variations may occur due to lighting, display differences, or human error.
            </p>
        </div>

        <!-- 4. Pricing & Payments -->
        <div>
            <h2 class="text-2xl font-semibold mb-2">4. Pricing & Payments</h2>
            <p>
                All prices are listed in INR and inclusive of taxes unless stated otherwise. 
                We reserve the right to update prices at any time. Payment must be completed 
                through available payment methods before order processing.
            </p>
        </div>

        <!-- 5. Shipping Policy -->
        <div>
            <h2 class="text-2xl font-semibold mb-2">5. Shipping & Delivery</h2>
            <p>
                Delivery timelines mentioned on our website are estimates and may vary 
                based on location, courier delays, weather, or unforeseen circumstances.
            </p>
        </div>

        <!-- 6. Returns & Refunds -->
        <div>
            <h2 class="text-2xl font-semibold mb-2">6. Returns & Refunds</h2>
            <p>
                Returns and refunds are processed as per our 
                <a href="{{ route('return.refund') }}" class="text-blue-600 hover:underline">Return & Refund Policy</a>.
            </p>
        </div>

        <!-- 7. Cancellation Policy -->
        <div>
            <h2 class="text-2xl font-semibold mb-2">7. Order Cancellation</h2>
            <p>
                You may cancel your order before it has been shipped.  
                Once dispatched, cancellation is no longer possible.
            </p>
        </div>

        <!-- 8. Intellectual Property -->
        <div>
            <h2 class="text-2xl font-semibold mb-2">8. Intellectual Property</h2>
            <p>
                All content including text, graphics, logos, and images are property of MyShop 
                and may not be used without prior written permission.
            </p>
        </div>

        <!-- 9. Limitation of Liability -->
        <div>
            <h2 class="text-2xl font-semibold mb-2">9. Limitation of Liability</h2>
            <p>
                MyShop shall not be held liable for any indirect, incidental, or consequential damages 
                arising from the use of our platform or services.
            </p>
        </div>

        <!-- 10. Changes to Terms -->
        <div>
            <h2 class="text-2xl font-semibold mb-2">10. Changes to These Terms</h2>
            <p>
                We may update these Terms & Conditions at any time.  
                Continued use of our website after changes means you accept the updated terms.
            </p>
        </div>

        <!-- 11. Contact -->
        <div>
            <h2 class="text-2xl font-semibold mb-2">11. Contact Us</h2>
            <p>
                For queries related to these Terms & Conditions, contact us at:  
                <a href="{{ route('contact') }}" class="text-blue-600 hover:underline">Contact Support</a>
            </p>
        </div>

    </div>
</div>
@endsection
