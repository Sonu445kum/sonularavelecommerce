@extends('layouts.admin')

@section('title', 'Manage Categories')

@section('content')
<div class="container mt-4">

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="fw-bold">📂 Manage Categories</h2>
        <a href="{{ route('admin.categories.create') }}" class="btn btn-success">
            <i class="bi bi-plus-circle"></i> Add New Category
        </a>
    </div>

    {{-- ✅ Flash Messages --}}
    @include('partials.messages')

    {{-- ✅ Categories Table --}}
    <div class="table-responsive shadow-sm rounded">
        <table class="table table-bordered table-hover align-middle" id="categoriesTable">
            <thead class="table-dark">
                <tr>
                    <th>#ID</th>
                    <th>Name</th>
                    <th>Slug</th>
                    <th>Created At</th>
                    <th class="text-center" style="width: 180px;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($categories as $category)
                    <tr id="category-row-{{ $category->id }}">
                        <td>{{ $category->id }}</td>
                        <td>{{ $category->name }}</td>
                        <td>{{ $category->slug }}</td>
                        <td>{{ $category->created_at->format('d M Y') }}</td>
                        <td class="text-center">
                            <a href="{{ route('admin.categories.edit', $category->id) }}" 
                               class="btn btn-sm btn-warning me-1">
                                <i class="bi bi-pencil-square"></i> Edit
                            </a>
                            <button 
                                class="btn btn-sm btn-danger delete-category" 
                                data-id="{{ $category->id }}" 
                                data-url="{{ route('admin.categories.destroy', $category->id) }}">
                                <i class="bi bi-trash"></i> Delete
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center text-muted py-4">
                            No categories found. <a href="{{ route('admin.categories.create') }}">Add one now!</a>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- ✅ Pagination --}}
    @if($categories->hasPages())
        <div class="mt-4 d-flex justify-content-center">
            {{ $categories->links('pagination::bootstrap-5') }}
        </div>
    @endif

    {{-- ✅ Categories Count Info --}}
    @if($categories->total() > 0)
        <div class="mt-3 text-muted small">
            Showing <strong>{{ $categories->firstItem() ?? 0 }}</strong> to 
            <strong>{{ $categories->lastItem() ?? 0 }}</strong> of 
            <strong>{{ $categories->total() }}</strong> categories
        </div>
    @endif
</div>

{{-- ✅ Delete Without Page Reload --}}
<script>
document.addEventListener('DOMContentLoaded', function () {
    const deleteButtons = document.querySelectorAll('.delete-category');

    deleteButtons.forEach(button => {
        button.addEventListener('click', function () {
            const categoryId = this.getAttribute('data-id');
            const url = this.getAttribute('data-url');
            const row = document.getElementById(`category-row-${categoryId}`);

            if (!confirm('⚠️ Are you sure you want to delete this category?')) return;

            fetch(url, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json',
                }
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    // Remove the row smoothly
                    row.style.transition = 'opacity 0.4s ease';
                    row.style.opacity = 0;
                    setTimeout(() => row.remove(), 400);

                    // Show success message
                    const alert = document.createElement('div');
                    alert.className = 'alert alert-success mt-3';
                    alert.textContent = data.message;
                    document.querySelector('.container').prepend(alert);

                    // Hide after 3 seconds
                    setTimeout(() => {
                        alert.classList.add('fade');
                        setTimeout(() => alert.remove(), 500);
                    }, 3000);
                } else {
                    alert('❌ Something went wrong!');
                }
            })
            .catch(() => alert('⚠️ Failed to delete category! Please try again.'));
        });
    });
});
</script>
@endsection
