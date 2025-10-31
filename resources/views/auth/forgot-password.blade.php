@extends('layouts.app')

@section('title', 'Forgot Password')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-gray-50 py-12 px-4">
    <div class="max-w-md w-full bg-white shadow-md rounded-xl p-8">
        <h2 class="text-center text-3xl font-extrabold text-gray-800 mb-6">Forgot Your Password? 🔑</h2>
        <p class="text-center text-gray-600 mb-6">Enter your email address and we’ll send you a reset link.</p>

        {{-- Flash messages --}}
        @include('partials.messages')

        <form method="POST" action="{{ route('forgot.send') }}">
            @csrf

            {{-- Email --}}
            <div class="mb-6">
                <label class="block text-gray-700 mb-1 font-semibold">Email Address</label>
                <input type="email" name="email" required autofocus
                       class="w-full border rounded-md p-2 focus:ring-2 focus:ring-indigo-500">
            </div>

            {{-- Submit --}}
            <button type="submit" 
                    class="w-full bg-indigo-600 text-white py-3 rounded-md font-semibold hover:bg-indigo-700 transition">
                Send Reset Link
            </button>
        </form>

        <p class="text-center text-sm text-gray-600 mt-6">
            Remembered your password? 
            <a href="{{ route('login.form') }}" class="text-indigo-600 font-medium hover:underline">Login</a>
        </p>
    </div>
</div>
@endsection
