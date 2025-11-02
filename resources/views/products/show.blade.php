{{-- ==========================================================
    Product Details Page – show.blade.php
    Displays a single product with related products & reviews
========================================================== --}}
@extends('layouts.app')

@section('title', $product->title . ' - MyShop')

@section('content')
<div class="container my-5">

    {{-- ✅ Breadcrumb --}}
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
            <li class="breadcrumb-item">
                <a href="{{ route('products.index', ['category' => $product->category->slug ?? '']) }}">
                    {{ $product->category->name ?? 'Uncategorized' }}
                </a>
            </li>
            <li class="breadcrumb-item active" aria-current="page">{{ $product->title }}</li>
        </ol>
    </nav>

    {{-- ✅ Product Details Section --}}
    <div class="row g-5">

        {{-- 🖼️ Product Image --}}
        <div class="col-md-6">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                <img src="{{ $product->featured_image ? asset($product->featured_image) : asset('images/no-image.png') }}" 
                     alt="{{ $product->title }}" class="img-fluid rounded-4 w-100">
            </div>

            {{-- ✅ Additional Images (Gallery) --}}
            @if ($product->images && $product->images->count() > 0)
                <div class="d-flex flex-wrap gap-2 mt-3">
                    @foreach ($product->images as $img)
                        <img src="{{ asset($img->url) }}" 
                             class="img-thumbnail rounded-3" width="90" height="90" alt="Gallery Image">
                    @endforeach
                </div>
            @endif
        </div>

        {{-- 🧾 Product Info --}}
        <div class="col-md-6">
            <h2 class="fw-bold mb-2">{{ $product->title }}</h2>
            <p class="text-muted mb-3">{{ $product->category->name ?? 'Uncategorized' }}</p>

            {{-- ⭐ Rating --}}
            <div class="text-warning mb-2">
                @for ($i = 0; $i < 5; $i++)
                    <i class="bi {{ $i < 4 ? 'bi-star-fill' : 'bi-star' }}"></i>
                @endfor
                <span class="text-secondary small">({{ $product->reviews->count() }} Reviews)</span>
            </div>

            {{-- 💰 Price --}}
            <div class="mb-3">
                @if ($product->discounted_price)
                    <h4 class="text-danger fw-bold mb-0">
                        ₹{{ number_format($product->discounted_price) }}
                    </h4>
                    <small class="text-muted text-decoration-line-through">
                        ₹{{ number_format($product->price) }}
                    </small>
                @else
                    <h4 class="fw-bold">₹{{ number_format($product->price) }}</h4>
                @endif
            </div>

            {{-- 🏷️ Stock Info --}}
            @if ($product->stock > 0)
                <p class="text-success fw-semibold">In Stock ({{ $product->stock }} available)</p>
            @else
                <p class="text-danger fw-semibold">Out of Stock</p>
            @endif

            {{-- 🛒 Add to Cart --}}
            <form action="{{ route('cart.add') }}" method="POST" class="mt-4">
                @csrf
                <input type="hidden" name="product_id" value="{{ $product->id }}">

                <div class="d-flex align-items-center gap-2 mb-3" style="max-width: 200px;">
                    <label for="quantity" class="form-label mb-0 small">Qty:</label>
                    <input type="number" name="quantity" id="quantity" min="1" 
                           max="{{ $product->stock }}" value="1" class="form-control">
                </div>

                <button type="submit" class="btn btn-primary btn-lg px-4" 
                        {{ $product->stock <= 0 ? 'disabled' : '' }}>
                    <i class="bi bi-cart-plus"></i> Add to Cart
                </button>
            </form>

            {{-- 📝 Description --}}
            <div class="mt-5">
                <h5 class="fw-semibold mb-3">Description</h5>
                <p class="text-secondary">{{ $product->description }}</p>
            </div>

            {{-- 🏷️ Extra Info (Meta Data) --}}
            @if (!empty($product->meta))
                @php
                    $meta = is_array($product->meta) ? $product->meta : json_decode($product->meta, true);
                @endphp
                @if (!empty($meta))
                    <ul class="list-group list-group-flush mt-3">
                        @foreach ($meta as $key => $value)
                            <li class="list-group-item">
                                <strong>{{ ucfirst($key) }}:</strong>
                                @if (is_array($value))
                                    {{ implode(', ', $value) }}
                                @else
                                    {{ $value }}
                                @endif
                            </li>
                        @endforeach
                    </ul>
                @endif
            @endif
        </div>
    </div>

    {{-- ⭐ Customer Reviews Section --}}
    <hr class="my-5">
    <div class="mt-4">
        <h4 class="fw-bold mb-3">Customer Reviews</h4>

        {{-- ✅ Show existing reviews --}}
        @forelse($product->reviews as $review)
            <div class="border rounded p-3 mb-3">
                <div class="d-flex justify-content-between align-items-center">
                    <strong>{{ $review->user->name ?? 'Anonymous User' }}</strong>
                    <span class="text-warning">
                        @for($i = 1; $i <= 5; $i++)
                            @if($i <= $review->rating)
                                ★
                            @else
                                ☆
                            @endif
                        @endfor
                    </span>
                </div>

                <p class="mt-2 mb-0">{{ $review->comment }}</p>

                {{-- 📷 Optional Review Images --}}
                @php
                    $reviewImages = is_string($review->images)
                        ? json_decode($review->images, true)
                        : $review->images;
                @endphp

                @if(!empty($reviewImages) && is_array($reviewImages))
                    <div class="mt-2">
                        @foreach($reviewImages as $img)
                            <img src="{{ asset('storage/'.$img) }}" 
                                 alt="Review Image" width="80" class="rounded me-2">
                        @endforeach
                    </div>
                @endif
            </div>
        @empty
            <p>No reviews yet. Be the first to review this product!</p>
        @endforelse
    </div>

    {{-- 📝 Review Form --}}
    @php
        $userHasPurchased = false;
        if (auth()->check()) {
            $userHasPurchased = \App\Models\Order::where('user_id', auth()->id())
                ->where('payment_status', 'paid')
                ->whereHas('orderItems', function ($q) use ($product) {
                    $q->where('product_id', $product->id);
                })
                ->exists();
        }
    @endphp

    @if(auth()->check() && $userHasPurchased)
        <div class="mt-5">
            <h4>Write a Review</h4>
            <form action="{{ route('reviews.store', $product->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="mb-3">
                    <label for="rating" class="form-label">Rating (1 to 5)</label>
                    <input type="number" name="rating" min="1" max="5" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label for="comment" class="form-label">Comment</label>
                    <textarea name="comment" class="form-control" rows="3" placeholder="Share your experience..."></textarea>
                </div>

                <div class="mb-3">
                    <label for="images" class="form-label">Upload Images (optional)</label>
                    <input type="file" name="images[]" multiple class="form-control">
                </div>

                <button type="submit" class="btn btn-primary">Submit Review</button>
            </form>
        </div>
    @elseif(auth()->check())
        <p class="text-muted mt-4">
            ⚠️ You can only review this product after purchasing it.
        </p>
    @else
        <p class="text-muted mt-4">
            🔐 Please <a href="{{ route('login') }}">login</a> to write a review.
        </p>
    @endif

    {{-- 🛍️ Related Products --}}
    @if (isset($related) && $related->count() > 0)
        <div class="mt-5">
            <h4 class="fw-bold mb-4">Related Products</h4>
            <div class="row g-4">
                @foreach ($related as $r)
                    <div class="col-6 col-md-3">
                        <div class="card border-0 shadow-sm rounded-4 h-100">
                            <img src="{{ $r->featured_image ? asset($r->featured_image) : asset('images/no-image.png') }}"
                                 alt="{{ $r->title }}" class="card-img-top rounded-top-4">
                            <div class="card-body text-center">
                                <h6 class="fw-semibold">{{ $r->title }}</h6>
                                <p class="text-primary fw-bold mb-1">
                                    ₹{{ number_format($r->discounted_price ?? $r->price) }}
                                </p>
                                <a href="{{ route('product.show', $r->slug) }}" 
                                   class="btn btn-sm btn-outline-primary w-100">View</a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

</div>
@endsection
