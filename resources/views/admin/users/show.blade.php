@extends('layouts.admin')

@section('title', 'User Details')

@section('content')
<div class="container mt-4">
    <h2 class="mb-4">User Details - {{ $user->name }}</h2>

    {{-- User Information --}}
    <div class="card mb-4 shadow-sm">
        <div class="card-body">
            <h5 class="card-title">User Information</h5>
            <p><strong>User ID:</strong> #{{ $user->id }}</p>
            <p><strong>Name:</strong> {{ $user->name }}</p>
            <p><strong>Email:</strong> {{ $user->email }}</p>
            <p><strong>Phone:</strong> {{ $user->phone ?? 'N/A' }}</p>
            <p><strong>Role:</strong> 
                @if($user->is_admin || $user->role === 'admin')
                    <span class="badge bg-danger">Admin</span>
                @elseif($user->role === 'vendor')
                    <span class="badge bg-warning">Vendor</span>
                @else
                    <span class="badge bg-primary">Customer</span>
                @endif
            </p>
            <p><strong>Status:</strong>
                @if($user->is_blocked)
                    <span class="badge bg-danger">Blocked</span>
                @elseif($user->is_active)
                    <span class="badge bg-success">Active</span>
                @else
                    <span class="badge bg-secondary">Inactive</span>
                @endif
            </p>
            <p><strong>Email Verified:</strong> 
                @if($user->email_verified_at)
                    <span class="badge bg-success">Yes</span> ({{ $user->email_verified_at->format('d M Y, h:i A') }})
                @else
                    <span class="badge bg-warning">No</span>
                @endif
            </p>
            <p><strong>Joined:</strong> {{ $user->created_at->format('d M Y, h:i A') }}</p>
        </div>
    </div>

    {{-- User Statistics --}}
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card shadow-sm">
                <div class="card-body text-center">
                    <h5 class="card-title">Total Orders</h5>
                    <h3 class="text-primary">{{ $user->orders()->count() }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card shadow-sm">
                <div class="card-body text-center">
                    <h5 class="card-title">Wishlist Items</h5>
                    <h3 class="text-danger">{{ $user->wishlist()->count() }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card shadow-sm">
                <div class="card-body text-center">
                    <h5 class="card-title">Reviews</h5>
                    <h3 class="text-warning">{{ $user->reviews()->count() }}</h3>
                </div>
            </div>
        </div>
    </div>

    {{-- Recent Orders --}}
    @if($user->orders()->count() > 0)
    <div class="card mb-4 shadow-sm">
        <div class="card-body">
            <h5 class="card-title">Recent Orders</h5>
            <div class="table-responsive">
                <table class="table table-sm">
                    <thead>
                        <tr>
                            <th>Order ID</th>
                            <th>Total</th>
                            <th>Status</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($user->orders()->latest()->take(5)->get() as $order)
                        <tr>
                            <td>
                                <a href="{{ route('admin.orders.show', $order->id) }}" class="text-primary">
                                    #{{ $order->id }}
                                </a>
                            </td>
                            <td>₹{{ number_format($order->total, 2) }}</td>
                            <td>
                                <span class="badge 
                                    @if(in_array(strtolower($order->status), ['delivered', 'completed'])) bg-success
                                    @elseif(strtolower($order->status) == 'cancelled') bg-danger
                                    @else bg-warning
                                    @endif">
                                    {{ ucfirst($order->status) }}
                                </span>
                            </td>
                            <td>{{ $order->created_at->format('d M Y') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endif

    <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">Back to Users</a>
</div>
@endsection

