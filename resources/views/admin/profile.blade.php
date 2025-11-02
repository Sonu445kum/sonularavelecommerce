@extends('layouts.admin')

@section('title', 'Profile')

@section('content')
<div class="container mx-auto py-10 px-6">
    <div class="max-w-2xl mx-auto bg-white rounded-2xl shadow-md p-6">
        {{-- ✅ Page Header --}}
        <h2 class="text-2xl font-semibold text-gray-800 mb-6 flex items-center justify-between">
            👤 My Profile
            <a href="{{ route('admin.dashboard') }}" class="text-blue-600 text-sm hover:underline">← Back to Dashboard</a>
        </h2>

        {{-- ✅ Profile Image Upload --}}
        <div class="flex flex-col items-center mb-6">
            <div class="relative">
                <img
                    src="{{ Auth::user()->profile_image ? asset('storage/' . Auth::user()->profile_image) : asset('images/default-avatar.png') }}"
                    alt="Profile Image"
                    class="w-32 h-32 rounded-full object-cover border-4 border-gray-200"
                >
                <a href="{{ route('admin.profile.edit') }}" class="absolute bottom-0 right-0 bg-blue-600 text-white rounded-full p-2 shadow hover:bg-blue-700">
                    <i class="fas fa-camera"></i>
                </a>
            </div>
        </div>

        {{-- ✅ User Info --}}
        <div class="space-y-4">
            <div>
                <label class="text-gray-600 text-sm font-medium">Full Name</label>
                <p class="text-lg text-gray-800 font-semibold">{{ Auth::user()->name }}</p>
            </div>

            <div>
                <label class="text-gray-600 text-sm font-medium">Email Address</label>
                <p class="text-lg text-gray-800">{{ Auth::user()->email }}</p>
            </div>

            <div>
                <label class="text-gray-600 text-sm font-medium">Role</label>
                <p class="text-lg text-gray-800">
                    {{ Auth::user()->is_admin ? 'Administrator' : 'User' }}
                </p>
            </div>
        </div>

        {{-- ✅ Edit Profile Button --}}
        <div class="mt-8 text-center">
            <a href="{{ route('admin.profile.edit') }}"
               class="bg-blue-600 text-white px-5 py-2 rounded-lg shadow hover:bg-blue-700 transition duration-300">
                ✏️ Edit Profile
            </a>
        </div>
    </div>
</div>
@endsection
