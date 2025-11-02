@extends('layouts.admin')

@section('title', 'Manage Users')

@section('content')
<div class="container mt-4">
    <h2 class="mb-3">Manage Users</h2>
    <a href="{{ route('admin.users.create') }}" class="btn btn-primary mb-3">Add New User</a>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>#ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Phone</th>
                <th>Role</th>
                <th>Status</th>
                <th>Joined</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($users as $user)
            <tr>
                <td>{{ $user->id }}</td>
                <td><strong>{{ $user->name }}</strong></td>
                <td>{{ $user->email }}</td>
                <td>{{ $user->phone ?? 'N/A' }}</td>
                <td>
                    @if($user->is_admin || $user->role === 'admin')
                        <span class="badge bg-danger">Admin</span>
                    @elseif($user->role === 'vendor')
                        <span class="badge bg-warning">Vendor</span>
                    @else
                        <span class="badge bg-primary">Customer</span>
                    @endif
                </td>
                <td>
                    @if($user->is_blocked)
                        <span class="badge bg-danger">Blocked</span>
                    @elseif($user->is_active)
                        <span class="badge bg-success">Active</span>
                    @else
                        <span class="badge bg-secondary">Inactive</span>
                    @endif
                </td>
                <td>{{ $user->created_at->format('d M Y') }}</td>
                <td>
                    <a href="{{ route('admin.users.show', $user->id) }}" class="btn btn-sm btn-info">View</a>
                    <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" style="display:inline;">
                        @csrf @method('DELETE')
                        <button class="btn btn-sm btn-danger" onclick="return confirm('Delete this user? This action cannot be undone.')">Delete</button>
                    </form>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="8" class="text-center py-3 text-muted">No users found</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    {{-- Pagination --}}
    <div class="mt-4">
        {{ $users->links('pagination::bootstrap-5') }}
    </div>

    {{-- Users Count Info --}}
    <div class="mt-2 text-muted">
        <small>Showing {{ $users->firstItem() ?? 0 }} to {{ $users->lastItem() ?? 0 }} of {{ $users->total() }} users</small>
    </div>
</div>
@endsection

