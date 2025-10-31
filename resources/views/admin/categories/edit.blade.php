@extends('layouts.admin')

@section('title', 'Edit Category')

@section('content')
<div class="container mt-4">
    <h2 class="mb-4">Edit Category</h2>

    <form action="{{ route('admin.categories.update', $category->id) }}" method="POST">
        @csrf @method('PUT')
        @include('partials.messages')

        <div class="mb-3">
            <label>Name:</label>
            <input type="text" name="name" class="form-control" value="{{ $category->name }}" required>
        </div>

        <div class="mb-3">
            <label>Slug:</label>
            <input type="text" name="slug" class="form-control" value="{{ $category->slug }}">
        </div>

        <button class="btn btn-primary">Update</button>
    </form>
</div>
@endsection
