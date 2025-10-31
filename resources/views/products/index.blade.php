@extends('layouts.app')

@section('title', 'All Products')

@section('content')
<div class="container py-5">
    <div class="row">

        {{-- ===========================
             FILTER SIDEBAR
        ============================ --}}
        <div class="col-md-3 mb-4">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <h5 class="fw-bold mb-3">Filters</h5>

                    <form method="GET" action="{{ route('products.index') }}">
                        <div class="mb-3">
                            <label class="form-label">Search</label>
                            <input type="text" name="q" value="{{ request('q') }}" class="form-control"
                                   placeholder="Search products...">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Category</label>
                            <select name="category" class="form-select">
                                <option value="">All Categories</option>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->slug }}" {{ request('category') == $cat->slug ? 'selected' : '' }}>
                                        {{ $cat->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="row mb-3">
                            <div class="col">
                                <label class="form-label">Min</label>
                                <input type="number" name="min_price" value="{{ request('min_price') }}" class="form-control" placeholder="0">
                            </div>
                            <div class="col">
                                <label class="form-label">Max</label>
                                <input type="number" name="max_price" value="{{ request('max_price') }}" class="form-control" placeholder="10000">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Sort By</label>
                            <select name="sort" class="form-select">
                                <option value="latest" {{ request('sort') == 'latest' ? 'selected' : '' }}>Latest</option>
                                <option value="price_asc" {{ request('sort') == 'price_asc' ? 'selected' : '' }}>Price: Low to High</option>
                                <option value="price_desc" {{ request('sort') == 'price_desc' ? 'selected' : '' }}>Price: High to Low</option>
                            </select>
                        </div>

                        <button type="submit" class="btn btn-primary w-100 mt-2">Apply Filters</button>
                        <a href="{{ route('products.index') }}" class="btn btn-outline-secondary w-100 mt-2">Reset</a>
                    </form>
                </div>
            </div>
        </div>

        {{-- ===========================
             PRODUCT GRID
        ============================ --}}
        <div class="col-md-9">
            <h3 class="fw-bold mb-4">
                All Products
                @if(request('q')) 
                    <small class="text-muted">for “{{ request('q') }}”</small> 
                @endif
            </h3>

            @if($products->count() > 0)
                <div class="row g-4">
                    @foreach ($products as $product)
                        <div class="col-md-4 mb-4">
                            <div class="card border-0 shadow-sm hover-shadow-sm">
                                <img 
                                    src="{{ $product->featured_image_url }}" 
                                    alt="{{ $product->title }}" 
                                    class="card-img-top img-fluid"
                                    style="width: 100%; height: 250px; object-fit: cover; border-top-left-radius: 0.75rem; border-top-right-radius: 0.75rem;"
                                    loading="lazy"
                                />

                                <div class="card-body text-center">
                                    <h5 class="card-title fw-bold">{{ $product->title }}</h5>
                                    <p class="card-text text-muted mb-2">₹{{ $product->price }}</p>
                                    <a href="{{ route('products.show', $product->slug) }}" class="btn btn-primary btn-sm">
                                        View Details
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="mt-4">
                    {{ $products->links() }}
                </div>
            @else
                <div class="alert alert-warning">
                    <strong>No products found!</strong> Try adjusting your filters or search.
                </div>
            @endif
        </div>
    </div>
</div>

<style>
    .hover-shadow-sm {
        transition: all 0.3s ease-in-out;
    }

    .hover-shadow-sm:hover {
        transform: translateY(-5px);
        box-shadow: 0 6px 16px rgba(0, 0, 0, 0.15);
    }
</style>
@endsection
