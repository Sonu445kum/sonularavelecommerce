@extends('layouts.app')

@section('title', 'Register')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-gray-50 py-12 px-4">
    <div class="max-w-md w-full bg-white shadow-md rounded-xl p-8">
        <h2 class="text-center text-3xl font-extrabold text-gray-800 mb-6">Create an Account 🛍️</h2>

        {{-- Flash messages --}}
        @include('partials.messages')

        <form method="POST" action="{{ route('register') }}">
            @csrf

            {{-- Name --}}
            <div class="mb-4">
                <label class="block text-gray-700 mb-1 font-semibold">Full Name</label>
                <input type="text" name="name" required autofocus
                       class="w-full border rounded-md p-2 focus:ring-2 focus:ring-indigo-500">
            </div>

            {{-- Email --}}
            <div class="mb-4">
                <label class="block text-gray-700 mb-1 font-semibold">Email Address</label>
                <input type="email" name="email" required
                       class="w-full border rounded-md p-2 focus:ring-2 focus:ring-indigo-500">
            </div>

            {{-- Password --}}
            <div class="mb-4">
                <label class="block text-gray-700 mb-1 font-semibold">Password</label>
                <input type="password" name="password" required
                       class="w-full border rounded-md p-2 focus:ring-2 focus:ring-indigo-500">
            </div>

            {{-- Confirm Password --}}
            <div class="mb-6">
                <label class="block text-gray-700 mb-1 font-semibold">Confirm Password</label>
                <input type="password" name="password_confirmation" required
                       class="w-full border rounded-md p-2 focus:ring-2 focus:ring-indigo-500">
            </div>

            {{-- Submit --}}
            <button type="submit" 
                    class="w-full bg-indigo-600 text-white py-3 rounded-md font-semibold hover:bg-indigo-700 transition">
                Create Account
            </button>
        </form>

        <p class="text-center text-sm text-gray-600 mt-6">
            Already have an account? 
            <a href="{{ route('login.form') }}" class="text-indigo-600 font-medium hover:underline">Login</a>
        </p>
    </div>
</div>
@endsection
