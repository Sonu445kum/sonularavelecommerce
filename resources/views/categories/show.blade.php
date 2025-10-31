@extends('layouts.app')

@section('title', $category->name . ' Products')

@section('content')
<div class="container mx-auto px-4 py-8">

    {{-- Category Header --}}
    <div class="flex justify-between items-center mb-8 border-b pb-4">
        <h1 class="text-3xl font-bold text-gray-800">{{ $category->name }}</h1>
        <div>
            <select class="border rounded-md p-2 text-sm">
                <option value="">Sort by</option>
                <option value="price_low_high">Price: Low to High</option>
                <option value="price_high_low">Price: High to Low</option>
                <option value="rating">Rating</option>
            </select>
        </div>
    </div>

    {{-- Banner / Category Description --}}
    @if($category->image)
        <div class="mb-6">
            <img src="{{ asset('storage/'.$category->image) }}" 
                 alt="{{ $category->name }}" 
                 class="w-full rounded-lg shadow-md h-64 object-cover">
        </div>
    @endif

    @if($category->description)
        <p class="text-gray-600 mb-6 text-lg">{{ $category->description }}</p>
    @endif

    {{-- Product Grid --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
        @forelse($category->products as $product)
            <div class="bg-white rounded-xl shadow-md overflow-hidden hover:shadow-lg transition duration-300">
                <a href="{{ route('products.show', $product->id) }}">
                    <img src="{{ asset('storage/'.$product->image) }}" 
                         alt="{{ $product->name }}" 
                         class="h-52 w-full object-cover">
                </a>
                <div class="p-4">
                    <h3 class="font-semibold text-gray-800 text-lg mb-1">
                        <a href="{{ route('products.show', $product->id) }}">{{ $product->name }}</a>
                    </h3>
                    <p class="text-gray-500 mb-2 text-sm">{{ Str::limit($product->description, 60) }}</p>
                    <div class="flex justify-between items-center">
                        <span class="text-indigo-600 font-bold text-lg">₹{{ number_format($product->price, 2) }}</span>
                        <form action="{{ route('cart.add', $product->id) }}" method="POST">
                            @csrf
                            <button type="submit" class="bg-indigo-600 text-white px-3 py-1 rounded hover:bg-indigo-700 text-sm">
                                Add to Cart
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        @empty
            <p class="col-span-4 text-center text-gray-600 py-12">No products found in this category.</p>
        @endforelse
    </div>

</div>
@endsection
