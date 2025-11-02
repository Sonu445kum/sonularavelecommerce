@extends('layouts.admin')

@section('title', 'Edit Profile')

@section('content')
<div class="container mx-auto py-10 px-6">
    <div class="max-w-2xl mx-auto bg-white rounded-2xl shadow-md p-6">
        {{-- ✅ Header --}}
        <h2 class="text-2xl font-semibold text-gray-800 mb-6 flex items-center justify-between">
            ✏️ Edit Profile
            <a href="{{ route('admin.profile.index') }}" class="text-blue-600 text-sm hover:underline">← Back to Profile</a>
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
                <label for="profile_image" class="block text-gray-700 font-medium mb-2">
                    Profile Image
                    <span class="text-sm text-gray-500 font-normal">(Optional - JPG, JPEG, or PNG, max 2MB)</span>
                </label>
                <div class="flex items-center gap-4">
                    <img
                        id="profile-image-preview"
                        src="{{ $user->profile_image ? asset('storage/' . $user->profile_image) : asset('images/default-avatar.png') }}"
                        alt="Profile Image Preview"
                        class="w-20 h-20 rounded-full border object-cover"
                        onerror="this.src='{{ asset('images/default-avatar.png') }}'"
                    >
                    <div class="flex-1">
                        <input 
                            type="file" 
                            id="profile_image"
                            name="profile_image" 
                            accept="image/jpeg,image/jpg,image/png"
                            class="border rounded-lg p-2 w-full file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100"
                            onchange="previewImage(this)"
                        >
                        <p class="text-xs text-gray-500 mt-1">
                            📷 Supported formats: JPG, JPEG, PNG | Maximum size: 2MB
                        </p>
                    </div>
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

<script>
    // Preview image before upload
    function previewImage(input) {
        if (input.files && input.files[0]) {
            const file = input.files[0];
            
            // Check file type
            const validTypes = ['image/jpeg', 'image/jpg', 'image/png'];
            if (!validTypes.includes(file.type)) {
                alert('❌ Please select a valid image file (JPG, JPEG, or PNG only)');
                input.value = '';
                return;
            }
            
            // Check file size (2MB = 2 * 1024 * 1024 bytes)
            if (file.size > 2 * 1024 * 1024) {
                alert('❌ Image size must be less than 2MB');
                input.value = '';
                return;
            }
            
            const reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('profile-image-preview').src = e.target.result;
            };
            reader.readAsDataURL(file);
        }
    }
</script>
@endsection
