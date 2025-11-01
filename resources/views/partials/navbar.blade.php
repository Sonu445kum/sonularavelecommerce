<nav class="bg-white shadow-md sticky top-0 z-50">
    <div class="max-w-7xl mx-auto flex items-center justify-between px-4 py-3">

        {{-- 🔹 Logo --}}
        <a href="{{ url('/') }}" class="flex items-center space-x-2">
            <img src="https://cdn-icons-png.flaticon.com/512/2331/2331970.png" alt="Logo" class="w-8 h-8">
            <span class="text-lg font-semibold text-gray-800">MyShop</span>
        </a>

        {{-- 🔹 Navigation Links --}}
        <div class="hidden md:flex items-center space-x-6">
            <a href="{{ url('/') }}" class="text-gray-700 hover:text-blue-600 font-medium">Home</a>
            <a href="{{ route('about') }}" class="text-gray-700 hover:text-blue-600 font-medium">About</a>
            <a href="{{ route('contact') }}" class="text-gray-700 hover:text-blue-600 font-medium">Contact</a>
            <a href="{{ route('products.index') }}" class="text-gray-700 hover:text-blue-600 font-medium">Products</a>
        </div>

        {{-- 🔹 Search Bar (Desktop) --}}
        <form action="{{ route('products.search') }}" method="GET" class="hidden md:flex w-1/3">
            <input type="text" name="query" placeholder="Search products..."
                class="w-full border border-gray-300 rounded-l-md px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400">
            <button type="submit" class="bg-blue-600 text-white px-4 rounded-r-md hover:bg-blue-700">Search</button>
        </form>

        {{-- 🔹 Right Section --}}
        <div class="flex items-center space-x-4">
            @auth
                {{-- 🧾 My Orders --}}
                <a href="{{ route('orders.index') }}" class="text-gray-700 hover:text-blue-600 text-sm font-medium">My Orders</a>

                {{-- 🛒 Cart with Dynamic Count --}}
                @php
                    $cartCount = \App\Models\CartItem::whereHas('cart', function ($query) {
                        $query->where('user_id', auth()->id());
                    })->count();
                @endphp

                <a href="{{ route('cart.index') }}" class="relative text-gray-700 hover:text-blue-600">
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

                {{-- 🚪 Logout --}}
                <form action="{{ route('logout') }}" method="POST" class="inline">
                    @csrf
                    <button class="bg-red-500 text-white px-3 py-1 rounded-md text-sm hover:bg-red-600">
                        Logout
                    </button>
                </form>
            @else
                {{-- 🔹 Auth Buttons --}}
                <a href="{{ route('login') }}" class="border border-blue-500 text-blue-500 px-3 py-1 rounded-md text-sm hover:bg-blue-500 hover:text-white transition">Login</a>
                <a href="{{ route('register') }}" class="bg-blue-600 text-white px-3 py-1 rounded-md text-sm hover:bg-blue-700">Sign Up</a>
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
    <div id="mobile-menu" class="hidden md:hidden bg-gray-100 border-t border-gray-200 px-4 py-3 space-y-2">
        <a href="{{ url('/') }}" class="block py-2 text-gray-700 hover:text-blue-600">Home</a>
        <a href="{{ route('about') }}" class="block py-2 text-gray-700 hover:text-blue-600">About</a>
        <a href="{{ route('contact') }}" class="block py-2 text-gray-700 hover:text-blue-600">Contact</a>
        <a href="{{ route('products.index') }}" class="block py-2 text-gray-700 hover:text-blue-600">Products</a>

        @auth
            {{-- 🧾 My Orders --}}
            <a href="{{ route('orders.index') }}" class="block py-2 text-gray-700 hover:text-blue-600">My Orders</a>

            {{-- 🛒 Cart (Mobile) --}}
            <a href="{{ route('cart.index') }}" class="flex items-center py-2 text-gray-700 hover:text-blue-600">
                🛒 Cart
                @if($cartCount > 0)
                    <span class="ml-2 bg-red-600 text-white text-xs rounded-full px-2">{{ $cartCount }}</span>
                @endif
            </a>

            {{-- 🚪 Logout --}}
            <form action="{{ route('logout') }}" method="POST" class="mt-2">
                @csrf
                <button class="w-full bg-red-500 text-white py-2 rounded-md hover:bg-red-600">
                    Logout
                </button>
            </form>
        @else
            {{-- 🔹 Auth Buttons (Mobile) --}}
            <a href="{{ route('login') }}" class="block w-full text-center border border-blue-500 text-blue-500 py-2 rounded-md mb-2 hover:bg-blue-500 hover:text-white">Login</a>
            <a href="{{ route('register') }}" class="block w-full text-center bg-blue-600 text-white py-2 rounded-md hover:bg-blue-700">Sign Up</a>
        @endauth
    </div>

    <script>
        document.getElementById('menu-btn').addEventListener('click', () => {
            document.getElementById('mobile-menu').classList.toggle('hidden');
        });
    </script>
</nav>
