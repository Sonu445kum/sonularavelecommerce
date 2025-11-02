<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name', 'MyShop E-Commerce') }}</title>

    <!-- ✅ Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    {{-- ✅ Tailwind CSS CDN --}}
    <script src="https://cdn.tailwindcss.com"></script>

    {{-- ✅ Google Font --}}
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>

    <!-- Font Awesome CDN -->
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
    </style>
</head>
<body class="bg-gray-50 text-gray-800">

    {{-- 🔹 Navbar --}}
    @include('partials.navbar')

    {{-- 🔹 Main Page Content --}}
    <div class="container mx-auto px-4 mt-4">
        @include('partials.messages')
        @yield('content')
    </div>

    {{-- 🔹 Footer --}}
    @include('partials.footer')

    <!-- ✅ Bootstrap JS (for carousel auto-slide, modal, dropdown, etc.) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <!-- 🧠 Carousel Auto-Slide Settings -->
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const carouselElement = document.querySelector('.carousel');
            if (carouselElement) {
                const carousel = new bootstrap.Carousel(carouselElement, {
                    interval: 3000, // ⏱️ Change image every 3 seconds
                    ride: 'carousel', // ✅ auto-slide enabled
                    pause: false, // no pause on hover
                    wrap: true // loop endlessly
                });
            }
        });
    </script>
</body>
</html>
