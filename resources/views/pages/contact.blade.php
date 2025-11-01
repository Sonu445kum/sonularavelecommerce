@extends('layouts.app')

@section('title', 'Contact Us')

@section('content')
<div class="max-w-4xl mx-auto px-4 py-12">
    <div class="text-center mb-8">
        <h1 class="text-4xl font-bold text-gray-800 mb-3">Contact <span class="text-blue-600">Us</span></h1>
        <p class="text-gray-600">We’d love to hear from you! Fill out the form below and our team will get back to you soon.</p>
    </div>

    <div class="bg-white rounded-2xl shadow-md p-8">
        <form method="POST" action="#">
            @csrf

            <div class="grid md:grid-cols-2 gap-6 mb-6">
                <div>
                    <label class="block text-gray-700 font-medium mb-2">Full Name</label>
                    <input type="text" name="name" placeholder="Your name"
                        class="w-full border border-gray-300 rounded-md px-4 py-2 focus:ring-2 focus:ring-blue-400 focus:outline-none" required>
                </div>

                <div>
                    <label class="block text-gray-700 font-medium mb-2">Email</label>
                    <input type="email" name="email" placeholder="you@example.com"
                        class="w-full border border-gray-300 rounded-md px-4 py-2 focus:ring-2 focus:ring-blue-400 focus:outline-none" required>
                </div>
            </div>

            <div class="mb-6">
                <label class="block text-gray-700 font-medium mb-2">Subject</label>
                <input type="text" name="subject" placeholder="How can we help you?"
                    class="w-full border border-gray-300 rounded-md px-4 py-2 focus:ring-2 focus:ring-blue-400 focus:outline-none" required>
            </div>

            <div class="mb-6">
                <label class="block text-gray-700 font-medium mb-2">Message</label>
                <textarea name="message" rows="5" placeholder="Write your message..."
                    class="w-full border border-gray-300 rounded-md px-4 py-2 focus:ring-2 focus:ring-blue-400 focus:outline-none" required></textarea>
            </div>

            <div class="text-center">
                <button type="submit"
                    class="bg-blue-600 text-white font-semibold px-6 py-2 rounded-md hover:bg-blue-700 transition">
                    Send Message
                </button>
            </div>
        </form>
    </div>

    <div class="text-center mt-10">
        <p class="text-gray-600 text-sm">📍 Our Office: 123 Market Street, New Delhi, India</p>
        <p class="text-gray-600 text-sm">📞 +91 98765 43210 | ✉️ support@myshop.com</p>
    </div>
</div>
@endsection
