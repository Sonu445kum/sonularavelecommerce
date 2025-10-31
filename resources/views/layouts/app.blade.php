{{-- =============================================
    app.blade.php
    → Main layout for frontend user interface
    → Includes Navbar, Footer, and Bootstrap + Tailwind
============================================= --}}
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name', 'MyShop E-Commerce') }}</title>

    {{-- Bootstrap CSS --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    {{-- Tailwind CSS CDN --}}
    <script src="https://cdn.tailwindcss.com"></script>

    {{-- Custom Styles --}}
    <style>
        body {
            background-color: #f9fafb;
            font-family: 'Poppins', sans-serif;
        }
        .hover-zoom:hover {
            transform: scale(1.03);
            transition: 0.3s;
        }
    </style>
</head>
<body class="bg-gray-50 text-gray-800">

    {{-- Include Navbar --}}
    @include('partials.navbar')

    {{-- Main Content --}}
    <main class="container my-4">
        @include('partials.messages')
        @yield('content')
    </main>

    {{-- Include Footer --}}
    @include('partials.footer')

    {{-- Bootstrap JS --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
