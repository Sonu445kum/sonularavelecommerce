@extends('layouts.app')

@section('title', 'All Products')

@section('content')
<div class="container mx-auto px-4 py-10">
    <div class="grid grid-cols-1 md:grid-cols-4 gap-8">

        {{-- ==========================
             🧭 FILTER SIDEBAR
        =========================== --}}
        <div class="bg-white shadow-lg rounded-2xl p-6 sticky top-20 h-fit">
            <h2 class="text-xl font-semibold mb-4 text-gray-800 flex items-center gap-2">
                <i class="fa-solid fa-sliders-h text-blue-600"></i> Filters
            </h2>

            <form method="GET" action="{{ route('products.index') }}" class="space-y-5">
                {{-- 🔍 Search --}}
                <div>
                    <label class="block text-gray-700 font-medium mb-1">Search</label>
                    <input type="text" name="q" value="{{ request('q') }}" placeholder="Search products..."
                        class="w-full rounded-lg border-gray-300 focus:ring-2 focus:ring-blue-500 focus:outline-none p-2.5">
                </div>

                {{-- 🏷 Category --}}
                <div>
                    <label class="block text-gray-700 font-medium mb-1">Category</label>
                    <select name="category"
                        class="w-full rounded-lg border-gray-300 focus:ring-2 focus:ring-blue-500 p-2.5">
                        <option value="">All Categories</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->slug }}" {{ request('category') == $cat->slug ? 'selected' : '' }}>
                                {{ $cat->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- 💰 Price Range --}}
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-gray-700 font-medium mb-1">Min</label>
                        <input type="number" name="min_price" value="{{ request('min_price') }}" placeholder="0"
                            class="w-full rounded-lg border-gray-300 focus:ring-2 focus:ring-blue-500 p-2.5">
                    </div>
                    <div>
                        <label class="block text-gray-700 font-medium mb-1">Max</label>
                        <input type="number" name="max_price" value="{{ request('max_price') }}" placeholder="10000"
                            class="w-full rounded-lg border-gray-300 focus:ring-2 focus:ring-blue-500 p-2.5">
                    </div>
                </div>

                {{-- 🔽 Sort --}}
                <div>
                    <label class="block text-gray-700 font-medium mb-1">Sort By</label>
                    <select name="sort"
                        class="w-full rounded-lg border-gray-300 focus:ring-2 focus:ring-blue-500 p-2.5">
                        <option value="latest" {{ request('sort') == 'latest' ? 'selected' : '' }}>Latest</option>
                        <option value="price_asc" {{ request('sort') == 'price_asc' ? 'selected' : '' }}>Price: Low to High</option>
                        <option value="price_desc" {{ request('sort') == 'price_desc' ? 'selected' : '' }}>Price: High to Low</option>
                    </select>
                </div>

                {{-- Buttons --}}
                <div class="space-y-3">
                    <button type="submit"
                        class="w-full bg-blue-600 text-white py-2.5 rounded-lg font-semibold hover:bg-blue-700 transition duration-300">
                        Apply Filters
                    </button>

                    <a href="{{ route('products.index') }}"
                        class="block text-center border border-gray-300 text-gray-700 py-2.5 rounded-lg hover:bg-gray-100 transition duration-300">
                        Reset
                    </a>
                </div>
            </form>
        </div>

        {{-- ==========================
             🛍 PRODUCT GRID
        =========================== --}}
        <div class="md:col-span-3">
            <h3 class="text-3xl font-bold text-gray-800 mb-8 flex items-center justify-between">
                <span>
                    All Products
                    @if(request('q')) 
                        <span class="text-gray-500 text-base font-normal">for “{{ request('q') }}”</span>
                    @endif
                </span>
                <span class="text-sm text-gray-600">
                    Showing {{ $products->count() }} of {{ $products->total() }} results
                </span>
            </h3>

            @if($products->count() > 0)
                <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-8">
                    @foreach ($products as $product)
                        <div class="group bg-white shadow-md rounded-2xl overflow-hidden hover:shadow-2xl transform hover:-translate-y-2 transition-all duration-300 relative">

                          {{-- ❤️ Wishlist Heart Icon --}}
                            @auth
                                @php
                                    $inWishlist = \App\Models\Wishlist::where('user_id', Auth::id())
                                        ->where('product_id', $product->id)
                                        ->exists();
                                @endphp
                                <form 
                                    action="{{ $inWishlist ? route('wishlist.remove') : route('wishlist.add') }}" 
                                    method="POST" 
                                    class="absolute top-3 right-3 z-10 wishlist-form"
                                    data-product-id="{{ $product->id }}"
                                >
                                    @csrf
                                    <input type="hidden" name="product_id" value="{{ $product->id }}">
                                    <button type="submit" class="wishlist-btn relative w-10 h-10 flex items-center justify-center rounded-full shadow hover:scale-110 transition">
                                        <span class="absolute inset-0 rounded-full transition-colors bg-white/80 group-hover:bg-red-100"></span>
                                        <i class="{{ $inWishlist ? 'fas' : 'far' }} fa-heart text-xl {{ $inWishlist ? 'text-red-600' : 'text-gray-400' }} relative z-10"></i>
                                    </button>
                                </form>
                            @else
                                <a href="{{ route('login') }}" 
                                class="absolute top-3 right-3 z-10 p-2 rounded-full shadow hover:scale-110 transition"
                                style="background: rgba(255,255,255,0.9); backdrop-filter: blur(5px);">
                                    <i class="far fa-heart text-gray-400 text-xl"></i>
                                </a>
                            @endauth


                            {{-- 🖼 Image --}}
                            <div class="relative">
                                <img src="{{ $product->featured_image_url ?? asset('images/no-image.png') }}" 
                                     alt="{{ $product->title }}" 
                                     class="w-full h-60 object-cover transition-transform duration-300 group-hover:scale-105">
                                @if($product->discount > 0)
                                    <span class="absolute top-3 left-3 bg-red-600 text-white text-xs font-bold px-2 py-1 rounded-full">
                                        -{{ $product->discount }}%
                                    </span>
                                @endif
                            </div>

                            {{-- 📦 Info --}}
                            <div class="p-4 text-center">
                                <h5 class="font-semibold text-gray-800 text-lg mb-1 truncate">{{ $product->title }}</h5>
                                <p class="text-gray-600 mb-2 line-clamp-2">{{ Str::limit($product->description, 60) }}</p>
                                <div class="flex justify-center items-center gap-2 mb-4">
                                    <span class="text-blue-600 font-bold text-lg">₹{{ number_format($product->price, 2) }}</span>
                                    @if($product->old_price)
                                        <span class="text-gray-400 line-through text-sm">₹{{ number_format($product->old_price, 2) }}</span>
                                    @endif
                                </div>

                                {{-- CTA --}}
                                <div class="flex justify-center gap-2">
                                    <a href="{{ route('products.show', $product->slug) }}"
                                        class="bg-blue-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-blue-700 transition duration-300">
                                        View Details
                                    </a>
                                    <form action="{{ route('cart.add') }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="product_id" value="{{ $product->id }}">
                                        <input type="hidden" name="quantity" value="1">
                                        <button type="submit"
                                            class="bg-green-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-green-700 transition duration-300">
                                            Add to Cart
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                {{-- Pagination --}}
                <div class="mt-10 flex justify-center">
                    {{ $products->links() }}
                </div>
            @else
                <div class="bg-yellow-50 border-l-4 border-yellow-400 text-yellow-800 p-5 rounded-lg text-center">
                    <strong>No products found!</strong> Try adjusting your filters or search terms.
                </div>
            @endif
        </div>
    </div>
</div>
<style>
    .wishlist-btn {
    width: 30px;
    height: 30px;
    display: flex;
    align-items: center;
    justify-content: center;
    background-color: #de0e0eff;
    border-radius: 50%;
}

.wishlist-btn i {
    font-size: 1rem;
}
</style>
{{-- ==========================
     ❤️ AJAX WISHLIST SCRIPT
========================== --}}
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const forms = document.querySelectorAll('.wishlist-form');

    forms.forEach(form => {
        form.addEventListener('submit', async (e) => {
            e.preventDefault();
            const button = form.querySelector('.wishlist-btn');
            const icon = button.querySelector('i'); // ← icon select karna zaruri hai

            try {
                const response = await fetch(form.action, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                    body: new FormData(form)
                });

                const data = await response.json();

                if (data.status) {
                    // Toggle icon color
                    icon.classList.toggle('text-red-500');
                    icon.classList.toggle('text-gray-400');

                    // Optional: toggle solid/regular heart
                    icon.classList.toggle('fas');
                    icon.classList.toggle('far');

                    showToast(data.message, 'success');
                } else {
                    showToast(data.message, 'error');
                }
            } catch (err) {
                console.error(err);
                showToast('Something went wrong!', 'error');
            }
        });
    });

    function showToast(message, type = 'success') {
        const toast = document.createElement('div');
        toast.className = `fixed top-5 right-5 px-4 py-3 rounded-lg text-white shadow-lg transition transform duration-500 ${
            type === 'error' ? 'bg-red-600' : 'bg-green-600'
        }`;
        toast.textContent = message;
        document.body.appendChild(toast);
        setTimeout(() => toast.remove(), 2500);
    }
});

</script>
@endpush
@endsection
