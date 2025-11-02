@extends('layouts.app')

@section('title', 'Edit Profile')

@section('content')
<div class="container mx-auto py-10 px-6">
    <div class="max-w-2xl mx-auto bg-white rounded-2xl shadow-md p-6">
        {{-- ✅ Header --}}
        <h2 class="text-2xl font-semibold text-gray-800 mb-6 flex items-center justify-between">
            ✏️ Edit Profile
            <a href="{{ route('admin.profile') }}" class="text-blue-600 text-sm hover:underline">← Back to Profile</a>
        </h2>

        {{-- ✅ Success / Error Messages --}}
        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-5">
                {{ session('success') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-5">
                <ul class="list-disc pl-5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- ✅ Edit Form --}}
        <form action="{{ route('admin.profile.update') }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            {{-- Full Name --}}
            <div class="mb-5">
                <label for="name" class="block text-gray-700 font-medium mb-2">Full Name</label>
                <input
                    type="text"
                    id="name"
                    name="name"
                    value="{{ old('name', $user->name) }}"
                    class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none"
                    required
                >
            </div>

            {{-- Email --}}
            <div class="mb-5">
                <label for="email" class="block text-gray-700 font-medium mb-2">Email Address</label>
                <input
                    type="email"
                    id="email"
                    name="email"
                    value="{{ old('email', $user->email) }}"
                    class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none"
                    required
                >
            </div>

            {{-- Profile Image --}}
            <div class="mb-6">
                <label class="block text-gray-700 font-medium mb-2">Profile Image</label>
                <div class="flex items-center gap-4">
                    <img
                        src="{{ $user->profile_image ? asset('storage/' . $user->profile_image) : asset('images/default-avatar.png') }}"
                        alt="Profile Image"
                        class="w-16 h-16 rounded-full border object-cover"
                    >
                    <input type="file" name="profile_image" class="border rounded-lg p-2 w-full">
                </div>
            </div>

            {{-- Submit --}}
            <div class="text-center">
                <button
                    type="submit"
                    class="bg-blue-600 text-white px-6 py-2 rounded-lg shadow hover:bg-blue-700 transition duration-300">
                    💾 Save Changes
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
