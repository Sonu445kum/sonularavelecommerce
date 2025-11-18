@extends('layouts.app')

@section('title', 'Return & Refund Policy')

@section('content')
<div class="max-w-5xl mx-auto px-4 py-12">

    <div class="text-center mb-8">
        <h1 class="text-4xl font-bold text-gray-800">Return & <span class="text-blue-600">Refund Policy</span></h1>
        <p class="text-gray-600 text-lg mt-2">
            Hassle-free returns to ensure a smooth shopping experience.
        </p>
    </div>

    <div class="bg-white p-8 rounded-2xl shadow-md space-y-7 text-gray-700">

        <div>
            <h2 class="text-2xl font-semibold mb-2">1. Return Eligibility</h2>
            <p>
                Items can be returned within <strong>7 days</strong> of delivery if they are damaged, defective, 
                or different from what was ordered.
            </p>
        </div>

        <div>
            <h2 class="text-2xl font-semibold mb-2">2. Non-returnable Items</h2>
            <p>
                Certain products like innerwear, hygiene products, and custom items cannot be returned unless defective.
            </p>
        </div>

        <div>
            <h2 class="text-2xl font-semibold mb-2">3. Refund Process</h2>
            <p>
                Refunds are processed within 3–7 working days after the returned product passes quality checks.
            </p>
        </div>

        <div>
            <h2 class="text-2xl font-semibold mb-2">4. Replacement Options</h2>
            <p>
                You may choose between a refund, replacement, or store credit after your return is approved.
            </p>
        </div>

        <div>
            <h2 class="text-2xl font-semibold mb-2">5. How to Initiate a Return</h2>
            <p>
                Contact our support team with your order number and issue details, and we’ll guide you further.
            </p>
        </div>

    </div>

</div>
@endsection
