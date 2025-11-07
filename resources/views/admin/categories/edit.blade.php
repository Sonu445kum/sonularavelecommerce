@extends('layouts.admin')

@section('title', 'Edit Category')

@section('content')
<div class="container mt-4">
    <h2 class="mb-4">✏️ Edit Category</h2>

    {{-- ✅ Flash messages --}}
    @include('partials.messages')

    <form action="{{ route('admin.categories.update', $category->id) }}" method="POST" novalidate>
        @csrf
        @method('PUT')

        {{-- 🏷️ Category Name --}}
        <div class="mb-3">
            <label for="cat-name" class="form-label fw-semibold">
                Category Name <span class="text-danger">*</span>
            </label>
            <input 
                type="text" 
                id="cat-name" 
                name="name"
                class="form-control @error('name') is-invalid @enderror"
                value="{{ old('name', $category->name) }}"
                placeholder="e.g. Wedding Venues"
                required
            >
            @error('name')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        {{-- 🔗 Slug (Manual Entry - Optional) --}}
        <div class="mb-3">
            <label for="cat-slug" class="form-label fw-semibold">
                Slug (Manual - Optional)
            </label>
            <input 
                type="text" 
                id="cat-slug" 
                name="slug"
                class="form-control @error('slug') is-invalid @enderror"
                value="{{ old('slug', $category->slug) }}"
                placeholder="e.g. wedding-venues"
            >
            <small class="text-muted">
                You can manually set the slug (e.g. <code>wedding-venues</code>).
                If left blank, it will auto-generate from the category name.
                <br>⚡ Duplicate slugs are automatically made unique.
            </small>
            @error('slug')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        {{-- 🪜 Parent Category (optional) --}}
        @if(isset($categories) && $categories->count() > 0)
            <div class="mb-3">
                <label for="parent_id" class="form-label fw-semibold">
                    Parent Category (optional)
                </label>
                <select id="parent_id" name="parent_id" class="form-select">
                    <option value="">-- No Parent (Main Category) --</option>
                    @foreach($categories as $parent)
                        <option 
                            value="{{ $parent->id }}" 
                            {{ old('parent_id', $category->parent_id) == $parent->id ? 'selected' : '' }}
                        >
                            {{ $parent->name }}
                        </option>
                    @endforeach
                </select>
            </div>
        @endif

        {{-- 📝 Description --}}
        <div class="mb-3">
            <label for="description" class="form-label fw-semibold">
                Description (optional)
            </label>
            <textarea 
                id="description" 
                name="description"
                class="form-control @error('description') is-invalid @enderror"
                rows="3"
                placeholder="Enter a short description"
            >{{ old('description', $category->description) }}</textarea>
            @error('description')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        {{-- 💾 Submit + Cancel Buttons --}}
        <div class="d-flex align-items-center mt-4">
            <button type="submit" class="btn btn-primary px-4">
                <i class="bi bi-pencil-square"></i> Update Category
            </button>
            <a href="{{ route('admin.categories.index') }}" class="btn btn-secondary ms-2">
                <i class="bi bi-x-circle"></i> Cancel
            </a>
        </div>
    </form>
</div>

{{-- Optional JavaScript to auto-suggest slug from name --}}
<script>
document.addEventListener('DOMContentLoaded', function () {
    const nameInput = document.getElementById('cat-name');
    const slugInput = document.getElementById('cat-slug');

    nameInput.addEventListener('input', function () {
        if (!slugInput.value.trim()) {
            slugInput.value = this.value
                .toLowerCase()
                .replace(/[^a-z0-9]+/g, '-')
                .replace(/^-+|-+$/g, '');
        }
    });
});
</script>
@endsection
