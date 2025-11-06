@extends('layouts.admin')

@section('title', 'Add Category')

@section('content')
<div class="container mt-4">
    <h2 class="mb-4">➕ Add New Category</h2>

    {{-- ✅ Success & Error Messages --}}
    @include('partials.messages')

    <form action="{{ route('admin.categories.store') }}" method="POST">
        @csrf

        {{-- 🏷️ Category Name --}}
        <div class="mb-3">
            <label for="cat-name" class="form-label fw-semibold">Category Name <span class="text-danger">*</span></label>
            <input type="text" id="cat-name" name="name" class="form-control @error('name') is-invalid @enderror"
                   placeholder="e.g. Wedding Venues" value="{{ old('name') }}" required>
            @error('name')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        {{-- 🔗 Slug (optional) --}}
        <div class="mb-3">
            <label for="cat-slug" class="form-label fw-semibold">Slug (optional)</label>
            <input type="text" id="cat-slug" name="slug" class="form-control @error('slug') is-invalid @enderror"
                   placeholder="e.g. wedding-venues" value="{{ old('slug') }}">
            <small class="text-muted">If left blank, it will be automatically generated from the name.</small>
            @error('slug')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        {{-- 🪜 Optional Parent Category (for sub-categories) --}}
        @if(isset($categories) && $categories->count() > 0)
        <div class="mb-3">
            <label for="parent_id" class="form-label fw-semibold">Parent Category (optional)</label>
            <select id="parent_id" name="parent_id" class="form-select">
                <option value="">-- No Parent (Main Category) --</option>
                @foreach($categories as $parent)
                    <option value="{{ $parent->id }}" {{ old('parent_id') == $parent->id ? 'selected' : '' }}>
                        {{ $parent->name }}
                    </option>
                @endforeach
            </select>
        </div>
        @endif

        {{-- 📝 Optional Description --}}
        <div class="mb-3">
            <label for="description" class="form-label fw-semibold">Description (optional)</label>
            <textarea id="description" name="description" class="form-control" rows="3"
                      placeholder="Enter a short description">{{ old('description') }}</textarea>
        </div>

        {{-- 💾 Submit Button --}}
        <button type="submit" class="btn btn-success px-4">
            <i class="bi bi-save"></i> Save Category
        </button>
    </form>
</div>

{{-- ✅ Auto-generate slug from name --}}
<script>
document.addEventListener('DOMContentLoaded', function () {
    const nameInput = document.getElementById('cat-name');
    const slugInput = document.getElementById('cat-slug');

    function slugify(text) {
        return text.toString().toLowerCase()
            .normalize('NFKD') // remove accents
            .replace(/[\u0300-\u036f]/g, '') // remove special chars
            .replace(/[^a-z0-9\s-]/g, '') // only letters, numbers & spaces
            .trim()
            .replace(/\s+/g, '-') // spaces → dash
            .replace(/-+/g, '-'); // multiple dashes → one
    }

    nameInput.addEventListener('input', function () {
        // Only auto-fill if slug is empty (user hasn't manually changed it)
        if (!slugInput.value.trim()) {
            slugInput.value = slugify(this.value);
        }
    });
});
</script>
@endsection
