@extends('layouts.admin')

@section('title', 'Edit Category')

@section('content')
<div class="container mt-4">
    <h2 class="mb-4">✏️ Edit Category</h2>

    {{-- ✅ Flash messages for success/error --}}
    @include('partials.messages')

    <form action="{{ route('admin.categories.update', $category->id) }}" method="POST">
        @csrf
        @method('PUT')

        {{-- 🏷️ Category Name --}}
        <div class="mb-3">
            <label for="cat-name" class="form-label fw-semibold">Category Name <span class="text-danger">*</span></label>
            <input type="text" id="cat-name" name="name"
                class="form-control @error('name') is-invalid @enderror"
                value="{{ old('name', $category->name) }}"
                placeholder="e.g. Wedding Venues" required>
            @error('name')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        {{-- 🔗 Slug (optional) --}}
        <div class="mb-3">
            <label for="cat-slug" class="form-label fw-semibold">Slug (optional)</label>
            <input type="text" id="cat-slug" name="slug"
                class="form-control @error('slug') is-invalid @enderror"
                value="{{ old('slug', $category->slug) }}"
                placeholder="e.g. wedding-venues">
            <small class="text-muted">If left blank, it will be automatically regenerated from the name.</small>
            @error('slug')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        {{-- 🪜 Optional Parent Category --}}
        @if(isset($categories) && $categories->count() > 0)
        <div class="mb-3">
            <label for="parent_id" class="form-label fw-semibold">Parent Category (optional)</label>
            <select id="parent_id" name="parent_id" class="form-select">
                <option value="">-- No Parent (Main Category) --</option>
                @foreach($categories as $parent)
                    <option value="{{ $parent->id }}" {{ old('parent_id', $category->parent_id) == $parent->id ? 'selected' : '' }}>
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
                      placeholder="Enter a short description">{{ old('description', $category->description) }}</textarea>
        </div>

        {{-- 💾 Update Button --}}
        <button type="submit" class="btn btn-primary px-4">
            <i class="bi bi-pencil-square"></i> Update Category
        </button>
        <a href="{{ route('admin.categories.index') }}" class="btn btn-secondary ms-2">Cancel</a>
    </form>
</div>

{{-- ✅ Auto-update slug when name changes --}}
<script>
document.addEventListener('DOMContentLoaded', function () {
    const nameInput = document.getElementById('cat-name');
    const slugInput = document.getElementById('cat-slug');

    function slugify(text) {
        return text.toString().toLowerCase()
            .normalize('NFKD')
            .replace(/[\u0300-\u036f]/g, '')
            .replace(/[^a-z0-9\s-]/g, '')
            .trim()
            .replace(/\s+/g, '-')
            .replace(/-+/g, '-');
    }

    // Auto-generate slug only if user hasn't manually modified it
    let userEditedSlug = false;
    slugInput.addEventListener('input', () => userEditedSlug = true);

    nameInput.addEventListener('input', function () {
        if (!userEditedSlug && !slugInput.value.trim()) {
            slugInput.value = slugify(this.value);
        }
    });
});
</script>
@endsection
