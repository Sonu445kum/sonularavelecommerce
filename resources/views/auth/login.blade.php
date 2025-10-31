@extends('layouts.app')

@section('title', 'Login')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-gray-50 py-12 px-4">
    <div class="max-w-md w-full bg-white shadow-md rounded-xl p-8">
        <h2 class="text-center text-3xl font-extrabold text-gray-800 mb-6">Welcome Back 👋</h2>

        {{-- Flash messages --}}
        @include('partials.messages')

        <form method="POST" action="{{ route('login') }}">
            @csrf

            {{-- Email --}}
            <div class="mb-4">
                <label class="block text-gray-700 mb-1 font-semibold">Email Address</label>
                <input type="email" name="email" required autofocus
                       class="w-full border rounded-md p-2 focus:ring-2 focus:ring-indigo-500">
            </div>

            {{-- Password --}}
            <div class="mb-4">
                <label class="block text-gray-700 mb-1 font-semibold">Password</label>
                <input type="password" name="password" required
                       class="w-full border rounded-md p-2 focus:ring-2 focus:ring-indigo-500">
            </div>

            {{-- Remember Me --}}
            <div class="flex items-center justify-between mb-6">
                <label class="flex items-center space-x-2">
                    <input type="checkbox" name="remember">
                    <span class="text-gray-600 text-sm">Remember me</span>
                </label>
                <a href="{{ route('forgot.form') }}" class="text-indigo-600 hover:underline text-sm">Forgot password?</a>
            </div>

            {{-- Submit --}}
            <button type="submit" 
                    class="w-full bg-indigo-600 text-white py-3 rounded-md font-semibold hover:bg-indigo-700 transition">
                Login
            </button>
        </form>

        <p class="text-center text-sm text-gray-600 mt-6">
            Don’t have an account? 
            <a href="{{ route('register.form') }}" class="text-indigo-600 font-medium hover:underline">Sign up</a>
        </p>
    </div>
</div>
@endsection
