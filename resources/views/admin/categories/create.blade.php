@extends('layouts.admin')

@section('title', 'Add Category')

@section('content')
<div class="container mt-4">
    <h2 class="mb-4">Add New Category</h2>

    <form action="{{ route('admin.categories.store') }}" method="POST">
        @csrf
        @include('partials.messages')

        <div class="mb-3">
            <label>Name:</label>
            <input type="text" name="name" class="form-control" placeholder="Category name" required>
        </div>

        <div class="mb-3">
            <label>Slug:</label>
            <input type="text" name="slug" class="form-control" placeholder="unique-category-slug">
        </div>

        <button class="btn btn-success">Save Category</button>
    </form>
</div>
@endsection
