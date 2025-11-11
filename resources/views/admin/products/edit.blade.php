@extends('layouts.admin')

@section('title', 'Edit Product')

@section('content')
<div class="container mt-4">
    <h2 class="mb-4">Edit Product</h2>

    <form action="{{ route('admin.products.update', $product->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        @include('partials.messages')

        {{-- Title --}}
        <div class="mb-3">
            <label>Title:</label>
            <input type="text" name="title" class="form-control" 
                   value="{{ old('title', $product->title) }}">
        </div>

        {{-- Price --}}
        <div class="mb-3">
            <label>Price:</label>
            <input type="number" name="price" class="form-control" 
                   value="{{ old('price', $product->price) }}">
        </div>

        {{-- Discounted Price --}}
        <div class="mb-3">
            <label>Discounted Price:</label>
            <input type="number" name="discounted_price" class="form-control" 
                   value="{{ old('discounted_price', $product->discounted_price) }}">
        </div>

        {{-- Stock --}}
        <div class="mb-3">
            <label>Stock:</label>
            <input type="number" name="stock" class="form-control" 
                   value="{{ old('stock', $product->stock) }}">
        </div>

        {{-- SKU --}}
        <div class="mb-3">
            <label>SKU:</label>
            <input type="text" name="sku" class="form-control" 
                   value="{{ old('sku', $product->sku) }}">
        </div>

        {{-- Category --}}
        <div class="mb-3">
            <label>Category:</label>
            <select name="category_id" class="form-control">
                @foreach($categories as $cat)
                    <option value="{{ $cat->id }}" 
                        {{ old('category_id', $product->category_id) == $cat->id ? 'selected' : '' }}>
                        {{ $cat->name }}
                    </option>
                @endforeach
            </select>
        </div>

        {{-- Featured Image --}}
        <div class="mb-3">
            <label>Featured Image:</label>
            <input type="file" name="featured_image" class="form-control">
            @if($product->featured_image)
                <img src="{{ asset('storage/' . $product->featured_image) }}" 
                     alt="Featured Image" class="img-thumbnail mt-2" width="150">
            @endif
        </div>

        {{-- Gallery Images --}}
        <div class="mb-3">
            <label>Gallery Images:</label>
            <input type="file" name="images[]" class="form-control" multiple>
            @if($product->images)
                @foreach(json_decode($product->images) as $img)
                    <img src="{{ asset('storage/' . $img) }}" 
                         alt="Gallery Image" class="img-thumbnail mt-2 me-2" width="100">
                @endforeach
            @endif
        </div>

        {{-- Active / Featured --}}
        <div class="form-check mb-3">
            <input type="checkbox" name="is_active" class="form-check-input" 
                   id="is_active" {{ old('is_active', $product->is_active) ? 'checked' : '' }}>
            <label class="form-check-label" for="is_active">Active</label>
        </div>

        <div class="form-check mb-3">
            <input type="checkbox" name="is_featured" class="form-check-input" 
                   id="is_featured" {{ old('is_featured', $product->is_featured) ? 'checked' : '' }}>
            <label class="form-check-label" for="is_featured">Featured</label>
        </div>

        <button class="btn btn-primary">Update Product</button>
    </form>
</div>
@endsection
