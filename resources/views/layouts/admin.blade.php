{{-- =============================================
    admin.blade.php
    → Modern Admin Layout with Sidebar + Topbar
============================================= --}}
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - {{ config('app.name', 'MyShop') }}</title>

    {{-- 🧩 Bootstrap, Tailwind & Icons --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f8fafc;
        }

        /* 🌙 Sidebar */
        .sidebar {
            width: 250px;
            background: #1e293b;
            color: white;
            height: 100vh;
            position: fixed;
            top: 0;
            left: 0;
            overflow-y: auto;
            transition: all 0.3s ease;
        }

        .sidebar h3 {
            background: #0f172a;
            padding: 15px;
            font-size: 1.1rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .sidebar a {
            color: #cbd5e1;
            display: flex;
            align-items: center;
            padding: 12px 20px;
            text-decoration: none;
            font-size: 0.95rem;
            border-left: 4px solid transparent;
            transition: all 0.3s ease;
        }

        .sidebar a i {
            font-size: 1.2rem;
            margin-right: 10px;
        }

        .sidebar a:hover,
        .sidebar a.active {
            background: #334155;
            color: #fff;
            border-left-color: #3b82f6;
        }

        /* 📦 Content Area */
        .content {
            margin-left: 250px;
            padding: 25px;
            min-height: 100vh;
            transition: margin-left 0.3s ease;
        }

        /* 🔝 Topbar */
        .topbar {
            background: #fff;
            border-radius: 12px;
            padding: 15px 25px;
            margin-bottom: 25px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .topbar h5 {
            margin: 0;
            font-weight: 600;
            color: #1e293b;
        }

        .topbar .user-info {
            display: flex;
            align-items: center;
            color: #1e293b;
            font-weight: 500;
        }

        .topbar .user-info i {
            font-size: 1.5rem;
            color: #3b82f6;
            margin-right: 8px;
        }

        /* Scrollbar */
        .sidebar::-webkit-scrollbar {
            width: 6px;
        }

        .sidebar::-webkit-scrollbar-thumb {
            background-color: #475569;
            border-radius: 3px;
        }
    </style>
</head>
<body>

    {{-- 🧭 Sidebar --}}
    <div class="sidebar">
        <h3 class="text-center text-white">🛍️ Admin Panel</h3>

        <a href="{{ route('admin.dashboard') }}" class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
            <i class="bi bi-speedometer2"></i> Dashboard
        </a>

        <a href="{{ route('admin.products.index') }}" class="{{ request()->routeIs('admin.products.*') ? 'active' : '' }}">
            <i class="bi bi-box-seam"></i> Product Management
        </a>

        <a href="{{ route('admin.categories.index') }}" class="{{ request()->routeIs('admin.categories.*') ? 'active' : '' }}">
            <i class="bi bi-tags"></i> Category Management
        </a>

        <a href="{{ route('admin.orders.index') }}" class="{{ request()->routeIs('admin.orders.*') ? 'active' : '' }}">
            <i class="bi bi-bag-check"></i> Order Management
        </a>

        <a href="{{ route('admin.coupons.index') }}" class="{{ request()->routeIs('admin.coupons.*') ? 'active' : '' }}">
            <i class="bi bi-ticket-detailed"></i> Coupon Management
        </a>

        <a href="{{ route('admin.wishlist.index') }}" class="{{ request()->routeIs('admin.wishlist.*') ? 'active' : '' }}">
            <i class="bi bi-heart"></i> Wishlist Management
        </a>

        <a href="{{ route('admin.payments.index') }}" class="{{ request()->routeIs('admin.payments.*') ? 'active' : '' }}">
            <i class="bi bi-cash-coin"></i> Payment Management
        </a>

        <a href="{{ route('admin.users.index') }}" class="{{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
            <i class="bi bi-people"></i> User Management
        </a>

        <hr class="border-gray-700 my-3">

        {{-- 🚪 Logout --}}
        <form action="{{ route('logout') }}" method="POST" class="px-3 mb-4">
            @csrf
            <button type="submit" class="btn btn-danger w-100">
                <i class="bi bi-box-arrow-right me-1"></i> Logout
            </button>
        </form>
    </div>

    {{-- 📦 Main Content --}}
    <div class="content">
        {{-- 🔝 Topbar --}}
        <div class="topbar">
            <h5>@yield('page_title', 'Admin Dashboard')</h5>
            <div class="user-info">
                <i class="bi bi-person-circle"></i>
                <span>{{ Auth::user()->name ?? 'Admin' }}</span>
            </div>
        </div>

        {{-- 💡 Dynamic Page Content --}}
        @yield('content')
    </div>

    {{-- ✅ Scripts --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
