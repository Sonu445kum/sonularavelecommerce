<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', config('app.name', 'MyShop E-Commerce'))</title>

    <!-- ✅ Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    {{-- ✅ Tailwind CSS CDN --}}
    <script src="https://cdn.tailwindcss.com"></script>

    {{-- ✅ Google Font --}}
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">

    {{-- ✅ Animate.css --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>

    <!-- ✅ Font Awesome CDN -->
    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"
        integrity="sha512-0Zt1P4X8TRH2qW+4xvPGXyHWZC2kD3G3Yw+Gg7yja+dI0vL1rLzY6NfPyxv8aPLvVuQePejF1j4Z9xP+/o3rXg=="
        crossorigin="anonymous"
        referrerpolicy="no-referrer"
    />

    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f9fafb;
        }

        /* ✨ Carousel Custom Look */
        .carousel-item img {
            height: 550px;
            object-fit: cover;
            border-radius: 20px;
        }

        .carousel-caption {
            background: rgba(0, 0, 0, 0.4);
            padding: 1rem 1.5rem;
            border-radius: 10px;
        }

        .carousel-caption h5 {
            font-size: 1.75rem;
            font-weight: 600;
        }

        .carousel-caption p {
            font-size: 1.1rem;
        }

        /* 🌟 Global Toast Animation */
        @keyframes slide-in {
            from { opacity: 0; transform: translateY(-20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .toast-message {
            animation: slide-in 0.4s ease-out;
        }
    </style>
</head>
<body class="bg-gray-50 text-gray-800 relative">

    {{-- 🔹 Navbar --}}
    @include('partials.navbar')

    {{-- ✅ SweetAlert popup (if used) --}}
    @include('sweetalert::alert')

    {{-- ✅ Global Toast (Session-based) --}}
    <div x-data="{ show: true }" class="fixed top-5 right-5 z-50 space-y-3">
        @if(session('success'))
            <div 
                x-show="show" 
                x-transition.opacity.duration.400ms
                x-init="setTimeout(() => show = false, 4000)"
                class="toast-message bg-green-600 text-white px-5 py-3 rounded-lg shadow-lg flex items-center space-x-3"
            >
                <i class="fa-solid fa-circle-check text-white text-lg"></i>
                <span class="font-semibold">{{ session('success') }}</span>
            </div>
        @endif

        @if(session('error'))
            <div 
                x-show="show" 
                x-transition.opacity.duration.400ms
                x-init="setTimeout(() => show = false, 4000)"
                class="toast-message bg-red-600 text-white px-5 py-3 rounded-lg shadow-lg flex items-center space-x-3"
            >
                <i class="fa-solid fa-triangle-exclamation text-white text-lg"></i>
                <span class="font-semibold">{{ session('error') }}</span>
            </div>
        @endif
    </div>

    {{-- 🔹 Main Page Content --}}
    <div class="container mx-auto px-4 mt-4">
        @yield('content')
    </div>

    {{-- 🔹 Footer --}}
    @include('partials.footer')

    <!-- ✅ Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    {{-- ⚡ Alpine.js for toast and dynamic UI --}}
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>

    <!-- 🧠 Carousel Auto-Slide Settings -->
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const carouselElement = document.querySelector('.carousel');
            if (carouselElement) {
                new bootstrap.Carousel(carouselElement, {
                    interval: 3000,
                    ride: 'carousel',
                    pause: false,
                    wrap: true
                });
            }
        });
    </script>
</body>
</html>
