<nav class="bg-gray-100 shadow-md sticky top-0 z-50 transition duration-300">
    <div class="max-w-7xl mx-auto flex items-center justify-between px-4 py-3">

        {{-- 🔹 Logo --}}
        <a href="{{ url('/') }}" class="flex items-center space-x-2">
            <img src="https://cdn-icons-png.flaticon.com/512/2331/2331970.png" alt="Logo" class="w-8 h-8">
            <span class="text-xl font-bold text-gray-900 tracking-tight">MyShop</span>
        </a>

        {{-- 🔹 Navigation Links (Desktop) --}}
        <div class="hidden md:flex items-center space-x-6">
            <a href="{{ url('/') }}" class="nav-link {{ request()->is('/') ? 'text-blue-600 font-semibold' : '' }}">Home</a>
            <a href="{{ route('about') }}" class="nav-link {{ request()->is('about') ? 'text-blue-600 font-semibold' : '' }}">About</a>
            <a href="{{ route('contact') }}" class="nav-link {{ request()->is('contact') ? 'text-blue-600 font-semibold' : '' }}">Contact</a>
            <a href="{{ route('products.index') }}" class="nav-link {{ request()->is('products*') ? 'text-blue-600 font-semibold' : '' }}">Products</a>
        </div>

        {{-- 🔍 Search Bar --}}
        <form action="{{ route('products.search') }}" method="GET" class="hidden md:flex w-1/3">
            <input type="text" name="query" placeholder="Search products..."
                class="w-full border border-gray-300 rounded-l-md px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400">
            <button type="submit" class="bg-blue-600 text-white px-4 rounded-r-md hover:bg-blue-700">Search</button>
        </form>

        {{-- 🔹 Right Section --}}
        <div class="flex items-center space-x-5">
            @auth
                @php
                    $wishlistCount = \App\Models\Wishlist::where('user_id', auth()->id())->count();
                    $cartCount = \App\Models\CartItem::whereHas('cart', fn($q) => $q->where('user_id', auth()->id()))->count();
                @endphp

                {{-- ❤️ Wishlist (Visible for All Logged Users) --}}
                <a href="{{ route('wishlist.index') }}" class="relative text-gray-800 hover:text-pink-600">
                    <i class="fas fa-heart text-xl"></i>
                    @if($wishlistCount > 0)
                        <span class="absolute -top-2 -right-2 bg-pink-600 text-white text-xs rounded-full px-1">
                            {{ $wishlistCount }}
                        </span>
                    @endif
                </a>

                {{-- 📦 Orders (Visible for All Logged Users) --}}
                <a href="{{ route('orders.index') }}" class="text-gray-800 hover:text-blue-600 text-sm font-medium">
                    <i class="fas fa-box"></i> My Orders
                </a>

                {{-- 🔔 Notifications Bell --}}
                <div class="relative">
                    <a href="{{ route('notifications.index') }}" class="text-gray-800 hover:text-blue-600 relative">
                        <i class="fas fa-bell text-xl"></i>
                        <span id="notification-badge" class="absolute -top-2 -right-2 bg-red-600 text-white text-xs rounded-full px-1 hidden">0</span>
                    </a>
                </div>

                {{-- 🛒 Cart (Visible for All Logged Users) --}}
                <a href="{{ route('cart.index') }}" class="relative text-gray-800 hover:text-blue-600">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                        stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M2.25 3h1.386c.51 0 .955.343 1.09.835l.383 1.435m0 0L6.75 14.25h10.5l1.636-8.98a1.125 1.125 0 00-1.11-1.32H4.119m.99 3.57h13.5" />
                    </svg>
                    @if($cartCount > 0)
                        <span class="absolute -top-2 -right-2 bg-red-600 text-white text-xs rounded-full px-1">
                            {{ $cartCount }}
                        </span>
                    @endif
                </a>

                {{-- 👤 Dropdown --}}
                <div class="relative">
                    <button id="user-menu-btn"
                        class="flex items-center space-x-1 text-gray-800 hover:text-blue-600 font-semibold focus:outline-none">
                        <span>{{ Auth::user()->is_admin ? '⚙️ Admin' : Auth::user()->name }}</span>
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>

                    {{-- Dropdown Menu --}}
                    <div id="user-dropdown"
    class="absolute right-0 mt-2 w-48 bg-white border border-gray-200 rounded-md shadow-lg hidden transition-all duration-200 z-50">

    {{-- 🔧 Admin Menu --}}
    @if(Auth::user()->is_admin)
        <a href="{{ route('admin.dashboard') }}" class="block px-4 py-2 text-gray-700 hover:bg-blue-50">
            📊 Dashboard
        </a>
        <a href="{{ route('admin.profile.index') }}" class="block px-4 py-2 text-gray-700 hover:bg-blue-50">
            👤 Profile
        </a>
        <!-- <a href="{{ route('admin.wishlist.index') }}" class="block px-4 py-2 text-gray-700 hover:bg-blue-50">
            ❤️ Wishlist
        </a> -->
    @else
        {{-- 👤 User Menu --}}
        <a href="{{ route('profile') }}" class="block px-4 py-2 text-gray-700 hover:bg-blue-50">
            👤 Profile
        </a>
    @endif

    <div class="border-t my-1"></div>
    <form action="{{ route('logout') }}" method="POST">
        @csrf
        <button type="submit"
            class="w-full text-left px-4 py-2 text-red-600 hover:bg-red-50">🚪 Logout</button>
    </form>
</div>
                </div>
            @else
                {{-- 🔹 Guest Buttons --}}
                <a href="{{ route('login') }}"
                    class="border border-blue-500 text-blue-500 px-3 py-1 rounded-md text-sm hover:bg-blue-500 hover:text-white transition">
                    Login
                </a>
                <a href="{{ route('register') }}"
                    class="bg-blue-600 text-white px-3 py-1 rounded-md text-sm hover:bg-blue-700 transition">
                    Sign Up
                </a>
            @endauth
        </div>

        {{-- 🔹 Mobile Toggle --}}
        <button id="menu-btn" class="md:hidden text-gray-700 focus:outline-none">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M3.75 5.25h16.5m-16.5 6h16.5m-16.5 6h16.5" />
            </svg>
        </button>
    </div>

    {{-- 🔹 Mobile Menu --}}
    <div id="mobile-menu"
        class="hidden md:hidden bg-gray-100 border-t border-gray-200 px-4 py-3 space-y-2 shadow-inner">
        <a href="{{ url('/') }}" class="mobile-link">Home</a>
        <a href="{{ route('about') }}" class="mobile-link">About</a>
        <a href="{{ route('contact') }}" class="mobile-link">Contact</a>
        <a href="{{ route('products.index') }}" class="mobile-link">Products</a>

        @auth
            <a href="{{ route('wishlist.index') }}" class="mobile-link text-pink-600 font-semibold">❤️ Wishlist</a>
            <a href="{{ route('orders.index') }}" class="mobile-link">📦 My Orders</a>
            <a href="{{ route('cart.index') }}" class="mobile-link flex items-center">
                🛒 Cart
                @if($cartCount > 0)
                    <span class="ml-2 bg-red-600 text-white text-xs rounded-full px-2">{{ $cartCount }}</span>
                @endif
            </a>

            @if(Auth::user()->is_admin)
                <a href="{{ route('admin.dashboard') }}" class="mobile-link">⚙️ Admin Dashboard</a>
                <a href="{{ route('admin.profile.index') }}" class="mobile-link">👤 Admin Profile</a>
            @else
                <a href="{{ route('profile') }}" class="mobile-link">👤 Profile</a>
            @endif

            <form action="{{ route('logout') }}" method="POST" class="mt-2">
                @csrf
                <button
                    class="w-full bg-red-500 text-white py-2 rounded-md hover:bg-red-600 transition font-medium">
                    Logout
                </button>
            </form>
        @else
            <a href="{{ route('login') }}"
                class="block w-full text-center border border-blue-500 text-blue-500 py-2 rounded-md mb-2 hover:bg-blue-500 hover:text-white">
                Login
            </a>
            <a href="{{ route('register') }}"
                class="block w-full text-center bg-blue-600 text-white py-2 rounded-md hover:bg-blue-700">
                Sign Up
            </a>
        @endauth
    </div>

    {{-- ✅ Styles --}}
    <style>
        .nav-link {
            @apply text-gray-800 hover:text-blue-600 font-medium transition;
        }

        .mobile-link {
            @apply block py-2 text-gray-800 hover:text-blue-600 transition font-medium;
        }
    </style>

    {{-- ✅ Scripts --}}
    <script>
        document.getElementById('menu-btn').addEventListener('click', () => {
            document.getElementById('mobile-menu').classList.toggle('hidden');
        });

        const userMenuBtn = document.getElementById('user-menu-btn');
        const userDropdown = document.getElementById('user-dropdown');

        if (userMenuBtn && userDropdown) {
            userMenuBtn.addEventListener('click', (e) => {
                e.stopPropagation();
                userDropdown.classList.toggle('hidden');
            });

            window.addEventListener('click', (e) => {
                if (!userDropdown.contains(e.target) && !userMenuBtn.contains(e.target)) {
                    userDropdown.classList.add('hidden');
                }
            });
        }

        // 🔔 Fetch unread notifications count
        @auth
        function fetchNotifications() {
            fetch('{{ route('notifications.unread') }}')
                .then(res => res.json())
                .then(data => {
                    const badge = document.getElementById('notification-badge');
                    if (data.count > 0) {
                        badge.textContent = data.count;
                        badge.classList.remove('hidden');
                    } else {
                        badge.classList.add('hidden');
                    }
                });
        }
        fetchNotifications();
        setInterval(fetchNotifications, 30000); // Update every 30 seconds
        @endauth
    </script>
</nav>
