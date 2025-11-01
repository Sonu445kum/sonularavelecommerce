@extends('layouts.app')

@section('title', $category->name . ' Products')

@section('content')
<div class="container mx-auto px-4 py-10">

    {{-- 🏷️ Category Header with Banner --}}
    @if($category->image)
        <div class="relative mb-10">
            <img src="{{ asset('storage/'.$category->image) }}" 
                 alt="{{ $category->name }}" 
                 class="w-full h-72 object-cover rounded-2xl shadow-lg">
            <div class="absolute inset-0 bg-gradient-to-t from-black/70 to-transparent rounded-2xl flex flex-col justify-end p-6">
                <h1 class="text-4xl font-bold text-white mb-2">{{ $category->name }}</h1>
                @if($category->description)
                    <p class="text-gray-200 text-sm md:text-base max-w-2xl">{{ $category->description }}</p>
                @endif
            </div>
        </div>
    @else
        <h1 class="text-3xl font-bold text-gray-800 mb-6">{{ $category->name }}</h1>
    @endif

    {{-- 🧩 Filter & Sort --}}
    <div class="flex flex-col sm:flex-row justify-between items-center mb-8 gap-3 border-b pb-4">
        <p class="text-gray-600 text-sm sm:text-base">
            Showing <span class="font-semibold text-gray-800">{{ $category->products->count() }}</span> products
        </p>
        <select class="border border-gray-300 rounded-md p-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none shadow-sm">
            <option value="">Sort by</option>
            <option value="price_low_high">Price: Low to High</option>
            <option value="price_high_low">Price: High to Low</option>
            <option value="rating">Rating</option>
        </select>
    </div>

    {{-- 🛍️ Product Grid --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-8">
        @forelse($category->products as $product)
            <div class="bg-white rounded-2xl overflow-hidden shadow-md hover:shadow-2xl transition duration-300 group relative border border-gray-100">
                
                {{-- Product Image --}}
                <a href="{{ route('products.show', $product->id) }}" class="block overflow-hidden">
                    <img src="{{ asset('storage/'.$product->image) }}" 
                         alt="{{ $product->name }}" 
                         class="h-56 w-full object-cover transform group-hover:scale-105 transition duration-500 ease-in-out">
                </a>

                {{-- Product Info --}}
                <div class="p-5">
                    <h3 class="font-semibold text-lg text-gray-800 mb-1 group-hover:text-blue-600 transition">
                        <a href="{{ route('products.show', $product->id) }}">{{ $product->name }}</a>
                    </h3>
                    <p class="text-gray-500 text-sm mb-3">{{ Str::limit($product->description, 60) }}</p>

                    {{-- Rating Stars (Optional UI only) --}}
                    <div class="flex items-center mb-3">
                        <span class="text-yellow-400">★</span>
                        <span class="text-yellow-400">★</span>
                        <span class="text-yellow-400">★</span>
                        <span class="text-yellow-400">★</span>
                        <span class="text-gray-300">★</span>
                        <span class="ml-1 text-xs text-gray-500">(120)</span>
                    </div>

                    {{-- Price + Add to Cart --}}
                    <div class="flex justify-between items-center">
                        <span class="text-indigo-600 font-bold text-lg">₹{{ number_format($product->price, 2) }}</span>
                        <form action="{{ route('cart.add', $product->id) }}" method="POST">
                            @csrf
                            <button type="submit" 
                                    class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg text-sm transition transform hover:scale-105 active:scale-95">
                                🛒 Add
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        @empty
            <p class="col-span-4 text-center text-gray-600 py-12 text-lg">
                No products found in this category 😔
            </p>
        @endforelse
    </div>
</div>
@endsection
