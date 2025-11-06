<!-- @extends('layouts.admin')

@section('title', 'Add Product')

@section('content')
<div class="container mt-4">
    <h2 class="mb-4">Add New Product</h2>

    <form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        @if($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="mb-3">
            <label>Product Title: <span class="text-danger">*</span></label>
            <input type="text" name="title" class="form-control" value="{{ old('title') }}" required placeholder="Enter product title">
        </div>

        <div class="mb-3">
            <label>Slug: <span class="text-danger">*</span></label>
            <input type="text" name="slug" class="form-control" value="{{ old('slug') }}" required placeholder="product-slug-url">
            <small class="text-muted">Auto-generated from title if left empty (will be created on submit)</small>
        </div>

        <div class="mb-3">
            <label>Description:</label>
            <textarea name="description" class="form-control" rows="4" placeholder="Enter product description">{{ old('description') }}</textarea>
        </div>

        <div class="mb-3">
            <label>Category:</label>
            <select name="category_id" class="form-control">
                <option value="">Select Category (Optional)</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label>Price: <span class="text-danger">*</span></label>
            <input type="number" step="0.01" name="price" class="form-control" value="{{ old('price') }}" required placeholder="0.00">
        </div>

        <div class="mb-3">
            <label>Discounted Price:</label>
            <input type="number" step="0.01" name="discounted_price" class="form-control" value="{{ old('discounted_price') }}" placeholder="0.00">
            <small class="text-muted">Leave empty if no discount</small>
        </div>

        <div class="mb-3">
            <label>Stock: <span class="text-danger">*</span></label>
            <input type="number" name="stock" class="form-control" value="{{ old('stock') }}" required placeholder="0" min="0">
        </div>

        <div class="mb-3">
            <label>SKU:</label>
            <input type="text" name="sku" class="form-control" value="{{ old('sku') }}" placeholder="Product SKU">
        </div>

        <div class="mb-3">
            <label>Featured Image:</label>
            <input type="file" name="featured_image" class="form-control" accept="image/*">
            <small class="text-muted">Upload main product image</small>
        </div>

        <div class="mb-3">
            <label>Additional Images:</label>
            <input type="file" name="images[]" class="form-control" accept="image/*" multiple>
            <small class="text-muted">You can upload multiple images</small>
        </div>

        <div class="mb-3">
            <div class="form-check">
                <input class="form-check-input" type="checkbox" name="is_active" value="1" id="is_active" checked>
                <label class="form-check-label" for="is_active">Active</label>
            </div>
        </div>

        <!-- <div class="mb-3">
            <div class="form-check">
                <input class="form-check-input" type="checkbox" name="is_featured" value="1" id="is_featured">
                <label class="form-check-label" for="is_featured">Featured Product</label>
            </div>
        </div> -->

        <!-- <button type="submit" class="btn btn-success">Save Product</button>
        <a href="{{ route('admin.products.index') }}" class="btn btn-secondary">Cancel</a>
    </form>
</div>

<script>
// Auto-generate slug from title
document.querySelector('input[name="title"]').addEventListener('input', function(e) {
    const slugInput = document.querySelector('input[name="slug"]');
    if (!slugInput.value || slugInput.dataset.manual !== 'true') {
        const slug = e.target.value
            .toLowerCase()
            .trim()
            .replace(/[^\w\s-]/g, '')
            .replace(/[\s_-]+/g, '-')
            .replace(/^-+|-+$/g, '');
        slugInput.value = slug;
    }
});

// Mark slug as manually edited
document.querySelector('input[name="slug"]').addEventListener('input', function() {
    this.dataset.manual = 'true';
});
</script>
@endsection --> -->
