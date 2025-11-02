@extends('layouts.admin')

@section('title', 'Manage Categories')

@section('content')
<div class="container mt-4">
    <h2 class="mb-3">Manage Categories</h2>
    <a href="{{ route('admin.categories.create') }}" class="btn btn-primary mb-3">Add New Category</a>

    @include('partials.messages')

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>#ID</th>
                <th>Name</th>
                <th>Slug</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($categories as $category)
            <tr>
                <td>{{ $category->id }}</td>
                <td>{{ $category->name }}</td>
                <td>{{ $category->slug }}</td>
                <td>
                    <a href="{{ route('admin.categories.edit', $category->id) }}" class="btn btn-sm btn-warning">Edit</a>
                    <form action="{{ route('admin.categories.destroy', $category->id) }}" method="POST" style="display:inline;">
                        @csrf @method('DELETE')
                        <button class="btn btn-sm btn-danger" onclick="return confirm('Delete this category?')">Delete</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    {{-- Pagination --}}
    <div class="mt-4">
        {{ $categories->links('pagination::bootstrap-5') }}
    </div>

    {{-- Categories Count Info --}}
    <div class="mt-2 text-muted">
        <small>Showing {{ $categories->firstItem() ?? 0 }} to {{ $categories->lastItem() ?? 0 }} of {{ $categories->total() }} categories</small>
    </div>
</div>
@endsection
