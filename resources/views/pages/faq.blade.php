@extends('layouts.app')

@section('title', 'FAQs')

@section('content')
<div class="max-w-6xl mx-auto px-4 py-12">

    <div class="text-center mb-10">
        <h1 class="text-4xl font-bold text-gray-800">Frequently Asked <span class="text-blue-600">Questions</span></h1>
        <p class="text-gray-600 text-lg mt-2">
            Find answers to the most commonly asked questions.
        </p>
    </div>

    <div class="space-y-6">

        @php
            $faqs = [
                [
                    'q' => 'How can I track my order?',
                    'a' => 'Once shipped, you will receive a tracking link via SMS or email to track your order in real-time.'
                ],
                [
                    'q' => 'How long does delivery take?',
                    'a' => 'Delivery usually takes 3–7 business days depending on your location.'
                ],
                [
                    'q' => 'What if I receive a damaged product?',
                    'a' => 'You can request a replacement or refund within 7 days of delivery by contacting support.'
                ],
                [
                    'q' => 'Do you offer Cash on Delivery (COD)?',
                    'a' => 'Yes, COD is available in most cities across India.'
                ],
                [
                    'q' => 'How can I contact customer support?',
                    'a' => 'You can reach us through the Contact Us page or email us at support@myshop.com.'
                ]
            ];
        @endphp

        @foreach ($faqs as $faq)
            <div class="bg-white p-6 rounded-xl shadow-md hover:shadow-lg transition">
                <h3 class="text-xl font-semibold text-gray-800 mb-2">{{ $faq['q'] }}</h3>
                <p class="text-gray-600">{{ $faq['a'] }}</p>
            </div>
        @endforeach

    </div>

</div>
@endsection
