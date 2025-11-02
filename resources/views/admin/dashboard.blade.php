@extends('layouts.admin')

@section('title', 'Admin Dashboard')

@section('content')
<div class="container py-4">

    {{-- ============================
         HEADER
    ============================= --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="fw-bold text-dark">
            <i class="bi bi-speedometer2 me-2 text-primary"></i> Admin Dashboard
        </h1>
        <span class="text-muted">Last Updated: {{ now()->format('d M, Y - h:i A') }}</span>
    </div>

    {{-- ============================
         SUMMARY CARDS
    ============================= --}}
    <div class="row g-3">

        {{-- Products --}}
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4 bg-gradient text-white" style="background: linear-gradient(135deg, #007bff, #00bfff);">
                <div class="card-body text-center py-4">
                    <i class="bi bi-box-seam fs-2 mb-2"></i>
                    <h6>Total Products</h6>
                    <h3 class="fw-bold">{{ $totalProducts ?? 0 }}</h3>
                </div>
            </div>
        </div>

        {{-- Orders --}}
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4 bg-gradient text-white" style="background: linear-gradient(135deg, #28a745, #6fdc8c);">
                <div class="card-body text-center py-4">
                    <i class="bi bi-bag-check fs-2 mb-2"></i>
                    <h6>Total Orders</h6>
                    <h3 class="fw-bold">{{ $totalOrders ?? 0 }}</h3>
                </div>
            </div>
        </div>

        {{-- Categories --}}
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4 bg-gradient text-white" style="background: linear-gradient(135deg, #ffc107, #ffdd57);">
                <div class="card-body text-center py-4">
                    <i class="bi bi-tags fs-2 mb-2"></i>
                    <h6>Total Categories</h6>
                    <h3 class="fw-bold">{{ $totalCategories ?? 0 }}</h3>
                </div>
            </div>
        </div>

        {{-- Users --}}
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4 bg-gradient text-white" style="background: linear-gradient(135deg, #dc3545, #f87171);">
                <div class="card-body text-center py-4">
                    <i class="bi bi-people fs-2 mb-2"></i>
                    <h6>Total Users</h6>
                    <h3 class="fw-bold">{{ $totalUsers ?? 0 }}</h3>
                </div>
            </div>
        </div>

        {{-- Revenue --}}
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4 bg-gradient text-white" style="background: linear-gradient(135deg, #17a2b8, #63e6be);">
                <div class="card-body text-center py-4">
                    <i class="bi bi-currency-rupee fs-2 mb-2"></i>
                    <h6>Total Revenue</h6>
                    <h3 class="fw-bold">₹{{ number_format($totalRevenue ?? 0, 2) }}</h3>
                </div>
            </div>
        </div>

        {{-- Pending Payments --}}
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4 bg-gradient text-white" style="background: linear-gradient(135deg, #6c757d, #adb5bd);">
                <div class="card-body text-center py-4">
                    <i class="bi bi-wallet2 fs-2 mb-2"></i>
                    <h6>Pending Payments</h6>
                    <h3 class="fw-bold">{{ $pendingPayments ?? 0 }}</h3>
                </div>
            </div>
        </div>

        {{-- Wishlist --}}
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4 bg-gradient text-white" style="background: linear-gradient(135deg, #20c997, #48dbfb);">
                <div class="card-body text-center py-4">
                    <i class="bi bi-heart fs-2 mb-2"></i>
                    <h6>Wishlist Items</h6>
                    <h3 class="fw-bold">{{ $wishlistCount ?? 0 }}</h3>
                </div>
            </div>
        </div>
    </div>

    {{-- ============================
         RECENT ORDERS
    ============================= --}}
    <div class="mt-5">
        <h4 class="fw-bold mb-3 text-dark">
            <i class="bi bi-receipt-cutoff me-2 text-primary"></i> Recent Orders
        </h4>
        <div class="card shadow-sm rounded-4">
            <div class="card-body p-0">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-primary">
                        <tr>
                            <th>#</th>
                            <th>User</th>
                            <th>Total</th>
                            <th>Status</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentOrders as $order)
                            <tr>
                                <td>{{ $order->id }}</td>
                                <td>{{ $order->user->name ?? 'Guest' }}</td>
                                <td>₹{{ number_format($order->total, 2) }}</td>
                                <td>
                                    <span class="badge bg-{{ in_array(strtolower($order->status), ['completed', 'delivered']) ? 'success' : 'warning' }}">
                                        {{ ucfirst($order->status) }}
                                    </span>
                                </td>
                                <td>{{ $order->created_at->format('d M Y') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-3 text-muted">No recent orders found</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- ============================
         RECENT USERS
    ============================= --}}
    <div class="mt-5">
        <h4 class="fw-bold mb-3 text-dark">
            <i class="bi bi-person-lines-fill me-2 text-success"></i> Recent Users
        </h4>
        <div class="card shadow-sm rounded-4">
            <div class="card-body p-0">
                <table class="table table-striped align-middle mb-0">
                    <thead class="table-success">
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Joined</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentUsers as $user)
                            <tr>
                                <td>{{ $user->id }}</td>
                                <td>{{ $user->name }}</td>
                                <td>{{ $user->email }}</td>
                                <td>{{ $user->created_at->format('d M Y') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center py-3 text-muted">No new users found</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- ============================
         RECENT PAYMENTS
    ============================= --}}
    <div class="mt-5 mb-5">
        <h4 class="fw-bold mb-3 text-dark">
            <i class="bi bi-credit-card-2-front me-2 text-info"></i> Recent Payments
        </h4>
        <div class="card shadow-sm rounded-4">
            <div class="card-body p-0">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-info">
                        <tr>
                            <th>ID</th>
                            <th>User</th>
                            <th>Amount</th>
                            <th>Status</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentPayments as $payment)
                            <tr>
                                <td>{{ $payment->id }}</td>
                                <td>{{ $payment->order && $payment->order->user ? $payment->order->user->name : 'N/A' }}</td>
                                <td>₹{{ number_format($payment->amount, 2) }}</td>
                                <td>
                                    <span class="badge bg-{{ $payment->status === 'success' ? 'success' : ($payment->status === 'pending' ? 'warning' : 'danger') }}">
                                        {{ ucfirst($payment->status) }}
                                    </span>
                                </td>
                                <td>{{ $payment->created_at->format('d M Y') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-3 text-muted">No recent payments found</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

{{-- ============================
     SMALL CSS TWEAKS
============================= --}}
<style>
.card:hover {
    transform: translateY(-4px);
    transition: 0.3s ease;
}
.table thead th {
    font-weight: 600;
}
</style>
@endsection
 