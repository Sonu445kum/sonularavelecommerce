@extends('layouts.app')

@section('title', 'Welcome to NewEcommerce')

@section('content')

<!-- ============================= -->
<!-- 🏠 HOME PAGE SECTION START -->
<!-- ============================= -->

<div class="container py-5">

    {{-- ======================= --}}
    {{-- 🎉 Hero Section --}}
    {{-- ======================= --}}
    <div class="text-center mb-5">
        <h1 class="fw-bold">Welcome to <span class="text-primary">NewEcommerce</span></h1>
        <p class="text-muted fs-5">Find the best deals on your favorite products!</p>
        <a href="{{ route('category.show', 'all') }}" class="btn btn-primary px-4">Shop Now</a>
    </div>

    {{-- ======================= --}}
    {{-- 🛍️ Featured Products --}}
    {{-- ======================= --}}
    <div class="mb-5">
        <h2 class="mb-4 border-bottom pb-2">🔥 Featured Products</h2>

        @if($featuredProducts->count() > 0)
            <div class="row g-4">
                @foreach($featuredProducts as $product)
                    <div class="col-md-3 col-sm-6">
                        <div class="card h-100 shadow-sm border-0">
                            <a href="{{ route('product.show', $product->slug) }}">
                                <img src="{{ asset('storage/' . $product->image) }}" class="card-img-top" alt="{{ $product->title }}">
                            </a>
                            <div class="card-body text-center">
                                <h5 class="card-title text-truncate">{{ $product->title }}</h5>
                                <p class="text-muted mb-1">₹{{ number_format($product->price, 2) }}</p>
                                <a href="{{ route('product.show', $product->slug) }}" class="btn btn-sm btn-outline-primary mt-2">
                                    View Details
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <p class="text-muted">No featured products available right now.</p>
        @endif
    </div>

    {{-- ======================= --}}
    {{-- 🗂️ Browse Categories --}}
    {{-- ======================= --}}
    <div>
        <h2 class="mb-4 border-bottom pb-2">🛒 Browse by Categories</h2>

        @if($categories->count() > 0)
            <div class="row g-4">
                @foreach($categories as $category)
                    <div class="col-md-4 col-sm-6">
                        <div class="card text-center shadow-sm border-0">
                            <a href="{{ route('category.show', $category->slug) }}">
                                <img src="{{ asset('storage/' . $category->image) }}" class="card-img-top" alt="{{ $category->name }}">
                            </a>
                            <div class="card-body">
                                <h5 class="card-title">{{ $category->name }}</h5>
                                <a href="{{ route('category.show', $category->slug) }}" class="btn btn-outline-secondary btn-sm">
                                    Explore
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <p class="text-muted">No categories available at the moment.</p>
        @endif
    </div>

</div>

<!-- ============================= -->
<!-- 🏠 HOME PAGE SECTION END -->
<!-- ============================= -->

@endsection
