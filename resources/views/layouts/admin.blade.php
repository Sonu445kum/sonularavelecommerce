{{-- =============================================
    admin.blade.php
    → Layout for admin dashboard
    → Includes sidebar, topbar, and Bootstrap + Tailwind
============================================= --}}
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - {{ config('app.name', 'MyShop') }}</title>

    {{-- Bootstrap & Tailwind --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>

    <style>
        body {
            font-family: 'Poppins', sans-serif;
        }
        .sidebar {
            width: 240px;
            background: #1e293b;
            color: white;
            height: 100vh;
            position: fixed;
        }
        .sidebar a {
            color: #cbd5e1;
            display: block;
            padding: 10px 20px;
            text-decoration: none;
        }
        .sidebar a:hover {
            background: #334155;
            color: #fff;
        }
        .content {
            margin-left: 240px;
            padding: 20px;
        }
    </style>
</head>
<body class="bg-gray-100">

    {{-- Sidebar --}}
    <div class="sidebar">
        <h3 class="p-3 fw-bold text-center text-xl border-bottom border-gray-700">Admin</h3>
        <a href="{{ route('admin.dashboard') }}">🏠 Dashboard</a>
        <a href="{{ route('admin.products.index') }}">📦 Products</a>
        <a href="{{ route('admin.categories.index') }}">🗂 Categories</a>
        <a href="{{ route('admin.orders.index') }}">🧾 Orders</a>
        <a href="{{ route('admin.coupons.index') }}">🎟 Coupons</a>
        <form action="{{ route('logout') }}" method="POST" class="mt-4">
            @csrf
            <button type="submit" class="btn btn-sm btn-danger mx-3">Logout</button>
        </form>
    </div>

    {{-- Content Section --}}
    <div class="content">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4 class="fw-bold">Admin Panel</h4>
        </div>
        @yield('content')
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
