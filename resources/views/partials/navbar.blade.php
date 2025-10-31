{{-- =============================================
    partials/navbar.blade.php
    → Responsive Flipkart/Amazon-style navbar
    → Includes logo, search bar, categories dropdown, cart & login icons
============================================= --}}
<nav class="bg-white shadow-md border-b border-gray-200 sticky top-0 z-50">
    <div class="container-fluid px-4 py-2 d-flex align-items-center justify-content-between">
        
        {{-- 🔹 Logo --}}
        <a href="{{ url('/') }}" class="flex items-center gap-2 text-decoration-none">
            <img src="https://cdn-icons-png.flaticon.com/512/2331/2331970.png" alt="Logo" class="w-8 h-8">
            <span class="fw-bold text-lg text-gray-800">MyShop</span>
        </a>

        {{-- 🔹 Search Bar --}}
        <form action="{{ route('products.search') }}" method="GET" class="d-none d-md-flex w-50 mx-3">
            <input type="text" name="query" placeholder="Search for products, brands and more..."
                   class="form-control rounded-start border-gray-300 focus:ring focus:ring-blue-200">
            <button type="submit" class="btn btn-primary rounded-end px-4">Search</button>
        </form>

        {{-- 🔹 Right Side Icons --}}
        <div class="d-flex align-items-center gap-3">
            @auth
                <a href="{{ route('orders.index') }}" class="text-decoration-none text-dark hover:text-blue-500">
                    <i class="bi bi-bag-check-fill"></i> My Orders
                </a>
                <a href="{{ route('cart.index') }}" class="relative text-decoration-none text-dark hover:text-blue-600">
                    <i class="bi bi-cart3 text-xl"></i>
                    <span class="badge bg-danger position-absolute top-0 start-100 translate-middle">
                        {{ session('cart_count', 0) }}
                    </span>
                </a>
                <form action="{{ route('logout') }}" method="POST" class="d-inline">
                    @csrf
                    <button class="btn btn-sm btn-outline-danger ms-2">Logout</button>
                </form>
            @else
                <a href="{{ route('login') }}" class="btn btn-outline-primary btn-sm">Login</a>
                <a href="{{ route('register') }}" class="btn btn-primary btn-sm">Sign Up</a>
            @endauth
        </div>

        {{-- 🔹 Mobile Menu Toggle --}}
        <button class="btn btn-outline-secondary d-md-none" type="button" data-bs-toggle="collapse" data-bs-target="#mobileMenu">
            <i class="bi bi-list"></i>
        </button>
    </div>

    {{-- 🔹 Mobile Dropdown Menu --}}
    <div class="collapse bg-gray-50" id="mobileMenu">
        <div class="p-3">
            <form action="{{ route('products.search') }}" method="GET" class="mb-2">
                <input type="text" name="query" class="form-control mb-2" placeholder="Search...">
                <button class="btn btn-primary w-100">Search</button>
            </form>

            @auth
                <a href="{{ route('orders.index') }}" class="d-block py-2 text-dark">My Orders</a>
                <a href="{{ route('cart.index') }}" class="d-block py-2 text-dark">My Cart</a>
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button class="btn btn-danger w-100 mt-2">Logout</button>
                </form>
            @else
                <a href="{{ route('login') }}" class="btn btn-outline-primary w-100 mb-2">Login</a>
                <a href="{{ route('register') }}" class="btn btn-primary w-100">Sign Up</a>
            @endauth
        </div>
    </div>
</nav>
