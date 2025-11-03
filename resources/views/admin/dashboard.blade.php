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

        {{-- 🔔 Notifications Dropdown --}}
        <div class="dropdown">
            <button class="btn btn-outline-primary position-relative" type="button" id="notificationDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                <i class="bi bi-bell fs-5"></i>
                @if(isset($notifications) && $notifications->where('is_read', false)->count() > 0)
                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                        {{ $notifications->where('is_read', false)->count() }}
                    </span>
                @endif
            </button>
            <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0" aria-labelledby="notificationDropdown" style="width: 350px;">
                <li class="dropdown-header fw-bold bg-light py-2 px-3">Notifications</li>
                @forelse($notifications as $notification)
                    <li class="px-3 py-2 border-bottom {{ $notification->is_read ? 'bg-light' : 'bg-white' }}">
                        <div class="d-flex align-items-start">
                            <i class="bi bi-bag-check text-primary fs-5 me-2"></i>
                            <div>
                                <div class="fw-semibold">{{ $notification->title }}</div>
                                <small class="text-muted d-block">{{ $notification->message }}</small>
                                <small class="text-secondary">{{ $notification->created_at->diffForHumans() }}</small>
                            </div>
                        </div>
                    </li>
                @empty
                    <li class="text-center text-muted py-3">No notifications found</li>
                @endforelse
            </ul>
        </div>
    </div>

    {{-- ============================
         SUMMARY CARDS
    ============================= --}}
    <div class="row g-4">
        @php
            $cards = [
                ['icon' => 'bi-box-seam', 'title' => 'Total Products', 'value' => $totalProducts ?? 0, 'color' => 'linear-gradient(135deg, #007bff, #00bfff)'],
                ['icon' => 'bi-bag-check', 'title' => 'Total Orders', 'value' => $totalOrders ?? 0, 'color' => 'linear-gradient(135deg, #28a745, #6fdc8c)'],
                ['icon' => 'bi-tags', 'title' => 'Total Categories', 'value' => $totalCategories ?? 0, 'color' => 'linear-gradient(135deg, #ffc107, #ffdd57)'],
                ['icon' => 'bi-people', 'title' => 'Total Users', 'value' => $totalUsers ?? 0, 'color' => 'linear-gradient(135deg, #dc3545, #f87171)'],
                ['icon' => 'bi-currency-rupee', 'title' => 'Total Revenue', 'value' => '₹' . number_format($totalRevenue ?? 0, 2), 'color' => 'linear-gradient(135deg, #17a2b8, #63e6be)'],
                ['icon' => 'bi-wallet2', 'title' => 'Pending Payments', 'value' => $pendingPayments ?? 0, 'color' => 'linear-gradient(135deg, #6c757d, #adb5bd)'],
                ['icon' => 'bi-heart', 'title' => 'Wishlist Items', 'value' => $wishlistCount ?? 0, 'color' => 'linear-gradient(135deg, #20c997, #48dbfb)'],
            ];
        @endphp

        @foreach($cards as $card)
            <div class="col-md-3 col-sm-6">
                <div class="stat-card" style="background: {{ $card['color'] }};">
                    <div class="stat-icon">
                        <i class="bi {{ $card['icon'] }}"></i>
                    </div>
                    <div class="stat-details">
                        <h6>{{ $card['title'] }}</h6>
                        <h3 class="fw-bold">{{ $card['value'] }}</h3>
                    </div>
                </div>
            </div>
        @endforeach
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
                    <thead class="table-primary text-dark">
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
                    <thead class="table-success text-dark">
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
                    <thead class="table-info text-dark">
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
     CUSTOM STYLES
============================= --}}
<style>
    .stat-card {
        border-radius: 20px;
        padding: 25px 20px;
        color: #222;
        text-align: center;
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        transition: all 0.3s ease;
        background-size: 200% 200%;
        animation: gradientMove 5s ease infinite;
    }
    @keyframes gradientMove {
        0% { background-position: 0% 50%; }
        50% { background-position: 100% 50%; }
        100% { background-position: 0% 50%; }
    }
    .stat-card:hover {
        transform: translateY(-6px) scale(1.02);
        box-shadow: 0 6px 18px rgba(0,0,0,0.15);
    }
    .stat-icon i {
        font-size: 2.4rem;
        color: rgba(0, 0, 0, 0.8);
        margin-bottom: 8px;
    }
    .stat-details h6 {
        font-size: 1rem;
        font-weight: 600;
        color: #222;
    }
    .stat-details h3 {
        font-size: 1.8rem;
        margin: 0;
        color: #111;
    }
    .dropdown-menu li {
        transition: background 0.2s ease;
    }
    .dropdown-menu li:hover {
        background-color: #f8f9fa;
    }
</style>
@endsection
