@extends('layouts.admin')

@section('title', 'Add New User')

@section('content')
<div class="container mt-4">
    <h2 class="mb-4">Add New User</h2>

    <form action="{{ route('admin.users.store') }}" method="POST">
        @csrf
        @include('partials.messages')

        <div class="mb-3">
            <label>Name: <span class="text-danger">*</span></label>
            <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
        </div>

        <div class="mb-3">
            <label>Email: <span class="text-danger">*</span></label>
            <input type="email" name="email" class="form-control" value="{{ old('email') }}" required>
            @error('email')
                <small class="text-danger">{{ $message }}</small>
            @enderror
        </div>

        <div class="mb-3">
            <label>Password: <span class="text-danger">*</span></label>
            <input type="password" name="password" class="form-control" required minlength="6">
        </div>

        <div class="mb-3">
            <label>Confirm Password: <span class="text-danger">*</span></label>
            <input type="password" name="password_confirmation" class="form-control" required minlength="6">
        </div>

        <button class="btn btn-success">Create User</button>
        <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">Cancel</a>
    </form>
</div>
@endsection

