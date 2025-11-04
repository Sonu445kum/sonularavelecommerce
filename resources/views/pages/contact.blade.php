@extends('layouts.app')

@section('title', 'Contact Us')

@section('content')
<div class="max-w-4xl mx-auto px-4 py-12 relative">

    {{-- 🌟 Header Section --}}
    <div class="text-center mb-8">
        <h1 class="text-4xl font-bold text-gray-800 mb-3">
            Contact <span class="text-blue-600">Us</span>
        </h1>
        <p class="text-gray-600">
            We’d love to hear from you! Fill out the form below and our team will get back to you soon.
        </p>
    </div>

    {{-- ✅ Toast Notification (Success Message) --}}
    @if(session('success'))
        <div 
            x-data="{ show: true }" 
            x-show="show" 
            x-transition.opacity.duration.500ms
            x-init="setTimeout(() => show = false, 4000)"
            class="fixed top-5 right-5 bg-green-600 text-white px-5 py-3 rounded-lg shadow-lg flex items-center space-x-3 z-50"
        >
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
            </svg>
            <span class="font-semibold">{{ session('success') }}</span>
        </div>
    @endif

    {{-- ❌ Error Messages --}}
    @if($errors->any())
        <div class="mb-6 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-md animate-fade-in">
            <ul class="list-disc list-inside">
                @foreach($errors->all() as $error)
                    <li>⚠️ {{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- 💌 Contact Form --}}
    <div class="bg-white rounded-2xl shadow-md p-8 transition hover:shadow-lg">
        <form method="POST" action="{{ route('contact.send') }}">
            @csrf

            <div class="grid md:grid-cols-2 gap-6 mb-6">
                <div>
                    <label class="block text-gray-700 font-medium mb-2">Full Name</label>
                    <input type="text" name="name" placeholder="Your name"
                        value="{{ old('name') }}"
                        class="w-full border border-gray-300 rounded-md px-4 py-2 focus:ring-2 focus:ring-blue-400 focus:outline-none" required>
                </div>

                <div>
                    <label class="block text-gray-700 font-medium mb-2">Email</label>
                    <input type="email" name="email" placeholder="you@example.com"
                        value="{{ old('email') }}"
                        class="w-full border border-gray-300 rounded-md px-4 py-2 focus:ring-2 focus:ring-blue-400 focus:outline-none" required>
                </div>
            </div>

            <div class="mb-6">
                <label class="block text-gray-700 font-medium mb-2">Subject</label>
                <input type="text" name="subject" placeholder="How can we help you?"
                    value="{{ old('subject') }}"
                    class="w-full border border-gray-300 rounded-md px-4 py-2 focus:ring-2 focus:ring-blue-400 focus:outline-none" required>
            </div>

            <div class="mb-6">
                <label class="block text-gray-700 font-medium mb-2">Message</label>
                <textarea name="message" rows="5" placeholder="Write your message..."
                    class="w-full border border-gray-300 rounded-md px-4 py-2 focus:ring-2 focus:ring-blue-400 focus:outline-none" required>{{ old('message') }}</textarea>
            </div>

            <div class="text-center">
                <button type="submit"
                    class="bg-blue-600 text-white font-semibold px-8 py-3 rounded-md hover:bg-blue-700 hover:scale-105 transition transform duration-200 ease-in-out">
                    Send Message ✉️
                </button>
            </div>
        </form>
    </div>

    {{-- 📍 Contact Info --}}
    <div class="text-center mt-10">
        <p class="text-gray-600 text-sm">📍 Our Office: 123 Market Street, New Delhi, India</p>
        <p class="text-gray-600 text-sm">📞 +91 98765 43210 | ✉️ support@myshop.com</p>
    </div>
</div>

{{-- ✨ Fade Animation --}}
<style>
@keyframes fade-in {
    from { opacity: 0; transform: translateY(-10px); }
    to { opacity: 1; transform: translateY(0); }
}
.animate-fade-in {
    animation: fade-in 0.5s ease-in-out;
}
</style>

{{-- ⚡ Alpine.js for Toast --}}
<script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
@endsection
