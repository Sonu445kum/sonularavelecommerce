@extends('layouts.app')

@section('title', 'My Wishlist')

@section('content')
<div class="container my-5">

    {{-- 🧡 Page Title --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold">
            <i class="fas fa-heart text-danger me-2"></i> My Wishlist
        </h2>
        <a href="{{ route('products.index') }}" class="btn btn-outline-primary btn-sm">
            <i class="fas fa-arrow-left me-1"></i> Continue Shopping
        </a>
    </div>

    @if($items->count() > 0)
        <div class="row g-4" id="wishlist-container">
            @foreach($items as $item)
                @php $product = $item->product; @endphp

                <div class="col-12 col-sm-6 col-md-4 col-lg-3 wishlist-card" data-id="{{ $product->id }}">
                    <div class="card border-0 shadow-sm position-relative rounded-4 overflow-hidden hover-card">

                        {{-- ❤️ Wishlist Heart Icon (Toggle AJAX) --}}
                        <button type="button" 
                                class="btn border-0 bg-transparent p-0 wishlist-btn position-absolute top-0 end-0 m-2 toggle-wishlist"
                                data-product-id="{{ $product->id }}">
                            <i class="fas fa-heart text-danger fs-4"></i>
                        </button>

                        {{-- 🖼 Product Image --}}
                        <div class="overflow-hidden position-relative">
                            @php
                                // Try featured_image first, then first image from images relationship, then fallback
                                $imageUrl = null;
                                if ($product->featured_image) {
                                    $imagePath = $product->featured_image;
                                    $imageUrl = (str_starts_with($imagePath, 'http://') || str_starts_with($imagePath, 'https://'))
                                        ? $imagePath
                                        : asset('storage/' . ltrim($imagePath, '/'));
                                }
                                // Fallback to first image in images relationship
                                elseif ($product->images && $product->images->count() > 0) {
                                    $imagePath = $product->images->first()->path;
                                    $imageUrl = (str_starts_with($imagePath, 'http://') || str_starts_with($imagePath, 'https://'))
                                        ? $imagePath
                                        : asset('storage/' . ltrim($imagePath, '/'));
                                }
                                // Final fallback
                                $imageUrl = $imageUrl ?? asset('images/default-product.jpg');
                                $productName = $product->title ?? $product->name ?? 'Product';
                            @endphp
                            <img src="{{ $imageUrl }}"
                                 alt="{{ $productName }}"
                                 class="card-img-top product-img"
                                 onerror="this.src='{{ asset('images/default-product.jpg') }}'">
                            @if($product->discount > 0)
                                <span class="badge bg-danger position-absolute top-0 start-0 m-2 px-3 py-2">
                                    -{{ $product->discount }}%
                                </span>
                            @endif
                        </div>

                        {{-- 📄 Product Info --}}
                        <div class="card-body text-center">
                            <h5 class="card-title fw-semibold text-dark">{{ $productName }}</h5>
                            <p class="text-muted mb-2 small">{{ Str::limit($product->description, 60) }}</p>

                            {{-- 💰 Price --}}
                            <div class="mb-2">
                                <span class="fw-bold text-primary fs-5">₹{{ number_format($product->price, 2) }}</span>
                                @if($product->old_price)
                                    <span class="text-muted text-decoration-line-through small ms-1">
                                        ₹{{ number_format($product->old_price, 2) }}
                                    </span>
                                @endif
                            </div>

                            {{-- ⭐ Rating Placeholder --}}
                            <div class="text-warning mb-3">
                                @for ($i = 1; $i <= 5; $i++)
                                    <i class="bi {{ $i <= 4 ? 'bi-star-fill' : 'bi-star' }}"></i>
                                @endfor
                            </div>

                            {{-- 🛒 Buttons --}}
                            <div class="d-flex justify-content-center gap-2">
                                <form action="{{ route('cart.add') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="product_id" value="{{ $product->id }}">
                                    <input type="hidden" name="quantity" value="1">
                                    <button type="submit" class="btn btn-outline-success btn-sm btn-custom">
                                        <i class="fas fa-shopping-cart me-1"></i> Add to Cart
                                    </button>
                                </form>

                                <a href="{{ route('products.show', $product->slug) }}"
                                   class="btn btn-primary btn-sm btn-custom">
                                    <i class="fas fa-eye me-1"></i> View
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- 🔄 Pagination --}}
        <div class="mt-4 d-flex justify-content-center">
            {{ $items->links() }}
        </div>

    @else
        <div class="text-center py-5">
            <i class="fas fa-heart-broken text-danger fs-1 mb-3"></i>
            <h4 class="fw-bold">Your wishlist is empty 💔</h4>
            <p class="text-muted mb-4">Browse products and add your favorites to the wishlist.</p>
            <a href="{{ route('products.index') }}" class="btn btn-primary">
                <i class="fas fa-shopping-bag me-1"></i> Start Shopping
            </a>
        </div>
    @endif
</div>

{{-- 💅 Styles --}}
<style>
.product-img {
    height: 230px;
    object-fit: cover;
    transition: transform 0.4s ease-in-out;
}
.hover-card:hover .product-img {
    transform: scale(1.07);
}
.btn-custom {
    border-radius: 25px;
    transition: 0.3s ease;
}
.btn-custom:hover {
    transform: scale(1.05);
}
.wishlist-btn i {
    transition: transform 0.3s ease, color 0.3s ease;
}
.wishlist-btn:hover i {
    transform: scale(1.15);
}
.hover-card {
    transition: all 0.3s ease;
}
.hover-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 20px rgba(0,0,0,0.15);
}
</style>

{{-- ⚡ Wishlist Toggle Script --}}
<script>
document.addEventListener("DOMContentLoaded", () => {
    const wishlistButtons = document.querySelectorAll(".toggle-wishlist");

    wishlistButtons.forEach(button => {
        button.addEventListener("click", async () => {
            const productId = button.dataset.productId;
            const icon = button.querySelector("i");

            try {
                const res = await fetch("{{ route('wishlist.remove') }}", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": "{{ csrf_token() }}"
                    },
                    body: JSON.stringify({ product_id: productId })
                });

                if (res.ok) {
                    // 💖 Toggle icon
                    icon.classList.toggle("fa-heart");
                    icon.classList.toggle("fa-heart-broken");
                    icon.classList.toggle("text-danger");
                    icon.classList.toggle("text-secondary");

                    // 🧹 Optionally remove card smoothly
                    const card = button.closest(".wishlist-card");
                    card.classList.add("fade-out");
                    setTimeout(() => card.remove(), 400);
                }
            } catch (err) {
                console.error("Error removing from wishlist:", err);
            }
        });
    });
});
</script>

{{-- ✨ Smooth Fade Animation --}}
<style>
.fade-out {
    opacity: 0;
    transform: scale(0.95);
    transition: all 0.4s ease;
}
</style>
@endsection
