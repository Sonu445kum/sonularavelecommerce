{{-- ==========================================================
     Product Search Results – search.blade.php
========================================================== --}}
@extends('layouts.app')

@section('title', 'Search Results for "' . e($query) . '"')

@section('content')
<div class="container py-5">

    {{-- 🔍 Search Heading --}}
    <h3 class="fw-bold mb-4">Search Results for "{{ $query }}"</h3>

    {{-- 🧭 Filters or Categories --}}
    @if($categories->count() > 0)
        <div class="mb-4">
            <p class="text-muted">Browse by category:</p>
            <div class="d-flex flex-wrap gap-2">
                @foreach($categories as $cat)
                    <a href="{{ route('products.index', ['category' => $cat->slug]) }}" class="btn btn-outline-secondary btn-sm">
                        {{ $cat->name }}
                    </a>
                @endforeach
            </div>
        </div>
    @endif

    {{-- 🛍️ Products Grid --}}
    @if($products->count() > 0)
        <div class="row g-4">
            @foreach($products as $product)
                <div class="col-6 col-md-3">
                    <div class="card border-0 shadow-sm rounded-4 h-100">
                        <img src="{{ $product->featured_image ? asset($product->featured_image) : asset('images/no-image.png') }}"
                             alt="{{ $product->title }}" class="card-img-top rounded-top-4">

                        <div class="card-body text-center">
                            <h6 class="fw-semibold">{{ $product->title }}</h6>
                            <p class="text-primary fw-bold mb-1">
                                ₹{{ number_format($product->discounted_price ?? $product->price) }}
                            </p>
                            {{-- ✅ Fixed Route --}}
                            <a href="{{ route('products.show', $product->slug) }}" 
                               class="btn btn-sm btn-outline-primary w-100">View Details</a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- 📄 Pagination --}}
        <div class="mt-4">
            {{ $products->links() }}
        </div>
    @else
        <div class="alert alert-info mt-4">
            No products found for "{{ $query }}". Try searching with different keywords.
        </div>
    @endif
</div>
@endsection
