@extends('layouts.app')

@section('title', 'Login')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-gray-50 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8 bg-white p-8 shadow-xl rounded-2xl border border-gray-100 transition-all duration-300 hover:shadow-2xl">
        
        {{-- 🔷 Heading --}}
        <div class="text-center">
            <h2 class="text-3xl font-extrabold text-gray-800">Welcome Back 👋</h2>
            <p class="text-sm text-gray-500 mt-2">Login to continue shopping with us 🛍️</p>
        </div>

        {{-- ✅ Flash Messages (Dynamic) --}}
        @if (session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative">
                <strong class="font-semibold">Success!</strong>
                <span class="block sm:inline">{{ session('success') }}</span>
            </div>
        @endif

        @if ($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative">
                <strong class="font-semibold">Oops!</strong>
                <span class="block sm:inline">Please fix the errors below.</span>
            </div>
        @endif

        {{-- 🧾 Login Form --}}
        <form method="POST" action="{{ route('login') }}" class="mt-6 space-y-5">
            @csrf

            {{-- Email --}}
            <div>
                <label class="block text-gray-700 mb-1 font-semibold">Email Address</label>
                <input type="email" name="email" value="{{ old('email') }}" required autofocus
                       class="w-full border rounded-md p-2 focus:ring-2 focus:ring-indigo-500 focus:outline-none @error('email') border-red-500 @enderror">
                @error('email')
                    <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Password --}}
            <div>
                <label class="block text-gray-700 mb-1 font-semibold">Password</label>
                <input type="password" name="password" required
                       class="w-full border rounded-md p-2 focus:ring-2 focus:ring-indigo-500 focus:outline-none @error('password') border-red-500 @enderror">
                @error('password')
                    <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Remember + Forgot Password --}}
            <div class="flex items-center justify-between">
                <label class="flex items-center space-x-2 text-sm">
                    <input type="checkbox" name="remember" class="text-indigo-600 focus:ring-indigo-500 rounded">
                    <span class="text-gray-600">Remember me</span>
                </label>
                <a href="{{ route('forgot.form') }}" class="text-indigo-600 hover:underline text-sm font-medium">
                    Forgot password?
                </a>
            </div>

            {{-- Login Button --}}
            <button type="submit"
                    class="w-full bg-indigo-600 text-white py-3 rounded-md font-semibold hover:bg-indigo-700 transition duration-300 transform hover:scale-[1.02]">
                Login
            </button>
        </form>

        {{-- Divider --}}
        <div class="flex items-center justify-center my-4">
            <span class="w-1/5 border-b border-gray-300"></span>
            <span class="mx-2 text-gray-400 text-sm">OR</span>
            <span class="w-1/5 border-b border-gray-300"></span>
        </div>

        {{-- Signup Link --}}
        <p class="text-center text-sm text-gray-600">
            Don’t have an account?
            <a href="{{ route('register.form') }}" 
               class="text-indigo-600 font-medium hover:underline hover:text-indigo-700 transition">
                Create one now
            </a>
        </p>
    </div>
</div>
@endsection
