{{-- ==========================================================
    Product Details Page – show.blade.php
    Displays a single product with all gallery images, reviews & related products
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
    <div class="row g-5 align-items-start">

        {{-- 🖼️ Gallery --}}
        <div class="col-md-6">
            <div class="card border-0 shadow-sm rounded-4 p-3">
                <div class="text-center">
                    <img id="mainImage" 
                         src="{{ isset($allImages[0]) ? asset($allImages[0]) : asset('images/no-image.png') }}" 
                         class="img-fluid rounded-4" 
                         style="max-height: 450px; object-fit: contain;" 
                         alt="{{ $product->title }}">
                </div>

                @if (!empty($allImages))
                    <div class="d-flex flex-wrap justify-content-center gap-2 mt-3">
                        @foreach ($allImages as $img)
                            <img src="{{ asset($img) }}" 
                                 onclick="changeMainImage('{{ asset($img) }}')" 
                                 class="img-thumbnail rounded-3 gallery-thumb" 
                                 style="width: 90px; height: 90px; cursor: pointer; object-fit: cover;"
                                 alt="Gallery Image">
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

        {{-- 🧾 Product Info --}}
        <div class="col-md-6">
            <h2 class="fw-bold mb-2">{{ $product->title }}</h2>
            <p class="text-muted mb-3">{{ $product->category->name ?? 'Uncategorized' }}</p>

            {{-- ⭐ Rating --}}
            @php
                $avgRating = round($product->reviews->avg('rating') ?? 0, 1);
                $totalReviews = $product->reviews->count();
            @endphp
            <div class="d-flex align-items-center mb-2">
                <div class="text-warning me-2" aria-hidden="true">
                    @for ($i = 1; $i <= 5; $i++)
                        @if ($i <= floor($avgRating))
                            <i class="bi bi-star-fill"></i>
                        @elseif ($i - $avgRating < 1 && $i - $avgRating > 0)
                            <i class="bi bi-star-half"></i>
                        @else
                            <i class="bi bi-star"></i>
                        @endif
                    @endfor
                </div>
                <div class="small text-secondary">
                    {{ $avgRating }} / 5 · ({{ $totalReviews }} Reviews)
                </div>
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
                    <input type="number"
                        name="quantity"
                        id="quantity"
                        min="1"
                        max="{{ $product->stock }}"
                        value="{{ session('last_selected_quantity_' . $product->id, 1) }}"
                        class="form-control">
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

            {{-- 🏷️ Meta --}}
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
        <h4 class="fw-bold mb-4">
            <i class="bi bi-chat-left-text text-primary"></i> Customer Reviews
        </h4>

        @forelse($product->reviews->sortByDesc('created_at') as $review)
            <div class="border rounded-4 p-4 mb-4 shadow-sm bg-white position-relative">
                {{-- 👤 Reviewer Info --}}
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <div>
                        <h6 class="fw-bold mb-1 text-dark">
                            <i class="bi bi-person-circle text-primary me-1"></i>
                            {{ $review->user->name ?? 'Anonymous User' }}
                        </h6>
                        <div class="text-warning small">
                            @for($i = 1; $i <= 5; $i++)
                                @if($i <= $review->rating)
                                    <i class="bi bi-star-fill"></i>
                                @else
                                    <i class="bi bi-star"></i>
                                @endif
                            @endfor
                            <span class="ms-2 text-muted">({{ $review->rating }}/5)</span>
                        </div>
                    </div>
                    <small class="text-muted">
                        {{ $review->created_at->format('M d, Y') }}
                    </small>
                </div>

                {{-- ✍️ Comment --}}
                @if(!empty($review->comment))
                    <p class="text-secondary mb-2" style="font-size: 0.95rem;">
                        {{ $review->comment }}
                    </p>
                @endif

{{-- 🖼️ Review Images --}}
@php
    $reviewImages = [];

    if (!empty($review->images)) {
        $reviewImages = is_string($review->images)
            ? json_decode($review->images, true)
            : (array) $review->images;
    }
@endphp

@if(!empty($reviewImages))
    <div class="d-flex flex-wrap gap-2 mt-2">
        @foreach($reviewImages as $img)
            @php
                // Clean up path
                $cleanPath = ltrim($img, '/');

                // If already starts with "storage/", use as is
                if (str_starts_with($cleanPath, 'storage/')) {
                    $publicPath = public_path($cleanPath);
                    $imgUrl = asset($cleanPath);
                } else {
                    // Otherwise prepend "storage/"
                    $publicPath = public_path('storage/' . $cleanPath);
                    $imgUrl = asset('storage/' . $cleanPath);
                }

                // Fallback if file doesn't exist
                if (!file_exists($publicPath)) {
                    $imgUrl = asset('images/no-image.png');
                }
            @endphp

            <a href="{{ $imgUrl }}" target="_blank">
                <img src="{{ $imgUrl }}"
                     alt="User Uploaded Review Photo"
                     class="rounded border shadow-sm"
                     style="width: 90px; height: 90px; object-fit: cover;">
            </a>
        @endforeach
    </div>
@endif







                {{-- 🎥 Review Video --}}
                @if(!empty($review->video_path))
                    @php
                        $videoUrl = str_starts_with($review->video_path, 'http')
                            ? $review->video_path
                            : asset('storage/' . ltrim($review->video_path, '/'));
                    @endphp
                    <div class="mt-3">
                        <video controls class="rounded shadow-sm w-100" style="max-width: 480px;">
                            <source src="{{ $videoUrl }}" type="video/mp4">
                            Your browser does not support HTML5 video.
                        </video>
                    </div>
                @endif
            </div>
        @empty
            <div class="alert alert-light border text-center">
                <p class="mb-0 text-muted">No reviews yet. Be the first to review this product!</p>
            </div>
        @endforelse
    </div>
    {{-- Pagination Links --}}
        <div class="mt-4 d-flex justify-content-center">
            {{ $reviews->links('vendor.pagination.bootstrap-5') }}
        </div>


    {{-- 📝 Review Form (Auth Only) --}}
    @auth
        <div class="mt-5">
            <h4 class="fw-bold mb-3"><i class="bi bi-star-half text-warning"></i> Write a Review</h4>
            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $err)
                            <li>{{ $err }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('reviews.store', $product->id) }}" 
                  method="POST" 
                  enctype="multipart/form-data" 
                  class="p-4 border rounded bg-light shadow-sm">

                @csrf

                {{-- ⭐ Star Rating --}}
                <div class="mb-3">
                    <label class="form-label fw-semibold">Rating <span class="text-danger">*</span></label>
                    <div class="star-rating d-flex flex-row-reverse justify-content-start">
                        <input type="radio" name="rating" id="star5" value="5"><label for="star5">★</label>
                        <input type="radio" name="rating" id="star4" value="4"><label for="star4">★</label>
                        <input type="radio" name="rating" id="star3" value="3"><label for="star3">★</label>
                        <input type="radio" name="rating" id="star2" value="2"><label for="star2">★</label>
                        <input type="radio" name="rating" id="star1" value="1"><label for="star1">★</label>
                    </div>
                    <p id="rating-text" class="mt-2 fw-semibold text-primary"></p>
                </div>

                {{-- ✍️ Comment --}}
                <div class="mb-3">
                    <label for="comment" class="form-label fw-semibold">Your Review</label>
                    <textarea name="comment" id="comment" rows="3" class="form-control" placeholder="Write your experience..."></textarea>
                </div>

               {{-- 🖼️ Upload Images (Drag & Drop) --}}
                <div class="mb-3">
                    <label class="form-label fw-semibold">Upload Images (optional)</label>
                    
                    <div id="dropArea" class="border border-dashed p-4 text-center rounded cursor-pointer">
                        <p class="text-muted">Drag & Drop Images or Click to Upload</p>
                        <input type="file" name="images[]" id="images" multiple accept="image/*" class="d-none">
                    </div>
                    <div id="imagePreview" class="d-flex flex-wrap mt-2"></div>
                </div>

                {{-- 🎥 Upload or Record Video --}}
                <div class="mb-3">
                    <label class="form-label fw-semibold">Upload or Record Video (optional)</label>
                    <div class="d-flex flex-column gap-2">
                        <input type="file" name="video" id="video" class="form-control" accept="video/mp4,video/webm,video/ogg">

                        <div class="d-flex gap-2">
                            <button type="button" id="recordBtn" class="btn btn-outline-primary btn-sm">🎥 Start Recording</button>
                            <button type="button" id="stopBtn" class="btn btn-outline-danger btn-sm d-none">⏹ Stop</button>
                        </div>

                        <video id="preview" class="mt-2 rounded shadow-sm d-none" width="320" height="240" controls></video>
                    </div>
                </div>

                <button type="submit" class="btn btn-success mt-3">Submit Review</button>
            </form>
        </div>
    @else
        <p class="text-muted mt-3">
            <a href="{{ route('login') }}" class="text-primary">Login</a> to post a review.
        </p>
    @endauth

    {{-- 🛍️ Related Products --}}
    @if(isset($relatedProducts) && $relatedProducts->count() > 0)
        <div class="mt-5">
            <h4 class="fw-bold mb-4">Related Products</h4>
            <div class="row g-4">
                @foreach ($relatedProducts as $r)
                    <div class="col-6 col-md-3">
                        <div class="card border-0 shadow-sm rounded-4 h-100">
                            <img src="{{ $r->featured_image ? asset($r->featured_image) : asset('images/no-image.png') }}"
                                 alt="{{ $r->title }}" class="card-img-top rounded-top-4">
                            <div class="card-body text-center">
                                <h6 class="fw-semibold">{{ $r->title }}</h6>
                                <p class="text-primary fw-bold mb-1">
                                    ₹{{ number_format($r->discounted_price ?? $r->price) }}
                                </p>
                                <a href="{{ route('products.show', $r->slug) }}" class="btn btn-sm btn-outline-primary w-100">View</a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

</div>

<style>
/* ⭐ Star Rating */
.star-rating {
    display: inline-flex;
    flex-direction: row-reverse;
    font-size: 1.8rem;
    cursor: pointer;
}
.star-rating input { display: none; }
.star-rating label { color: #ccc; transition: color 0.2s; cursor: pointer; }
.star-rating input:checked ~ label,
.star-rating label:hover,
.star-rating label:hover ~ label { color: #ffc107; }

/* Customer Review Cards */
#customer-reviews .border { border: 1px solid #e5e7eb !important; }
#customer-reviews h6 { font-size: 1rem; }
#customer-reviews video { border: 1px solid #dee2e6; }
#customer-reviews img:hover { transform: scale(1.05); transition: 0.3s ease-in-out; }


#dropArea {
    border: 2px dashed #ccc;
    border-radius: 10px;
    padding: 20px;
    text-align: center;
    transition: background 0.2s, border-color 0.2s;
}

#dropArea.border-primary {
    border-color: #0d6efd !important;
}

#dropArea.bg-light {
    background-color: #f8f9fa;
}

</style>

<script>
document.addEventListener('DOMContentLoaded', function() {

    // =============================
    // ⭐ Star Rating Display
    // =============================
    const stars = document.querySelectorAll('.star-rating input');
    const ratingText = document.getElementById('rating-text');
    const ratingLabels = {
        1: "Very Bad 😞",
        2: "Bad 😕",
        3: "Average 🙂",
        4: "Good 😄",
        5: "Excellent 🤩"
    };

    stars.forEach(star => {
        star.addEventListener('change', () => {
            ratingText.textContent = ratingLabels[star.value];
        });
    });

    // =============================
// ⭐ Image Drag & Drop + Preview
// =============================
const dropArea = document.getElementById('dropArea');
const imageInput = document.getElementById('images');
const previewContainer = document.getElementById('imagePreview');

if (dropArea && imageInput && previewContainer) {

    // 🖱️ Click to open file dialog
    dropArea.addEventListener('click', () => imageInput.click());

    // 📂 Manual select
    imageInput.addEventListener('change', () => handleFiles(imageInput.files));

    // 🎯 Highlight on drag
    ['dragenter', 'dragover'].forEach(eventName => {
        dropArea.addEventListener(eventName, e => {
            e.preventDefault();
            e.stopPropagation();
            dropArea.classList.add('border-primary', 'bg-light');
        });
    });

    // ❌ Remove highlight
    ['dragleave', 'drop'].forEach(eventName => {
        dropArea.addEventListener(eventName, e => {
            e.preventDefault();
            e.stopPropagation();
            dropArea.classList.remove('border-primary', 'bg-light');
        });
    });

    // 📥 Handle file drop
    dropArea.addEventListener('drop', e => {
        e.preventDefault();
        e.stopPropagation();
        const files = e.dataTransfer.files;
        handleFiles(files);
    });

    // 🖼️ Handle and Preview Files
    function handleFiles(files) {
        previewContainer.innerHTML = '';

        // ✅ Extended file types (fix)
        const allowedTypes = [
            'image/jpeg',
            'image/jpg',
            'image/png',
            'image/webp',
            'image/pjpeg',
            'image/jfif'
        ];

        const validFiles = [];

        Array.from(files).forEach(file => {
            if (!allowedTypes.includes(file.type)) return; // skip invalid
            validFiles.push(file);

            const reader = new FileReader();
            reader.onload = e => {
                const img = document.createElement('img');
                img.src = e.target.result;
                img.classList.add('rounded', 'border', 'shadow-sm', 'm-1');
                img.style.width = '90px';
                img.style.height = '90px';
                img.style.objectFit = 'cover';
                img.title = file.name;
                previewContainer.appendChild(img);
            };
            reader.readAsDataURL(file);
        });

        // ✅ Assign selected files back to input
        setTimeout(() => {
            const dt = new DataTransfer();
            validFiles.forEach(f => dt.items.add(f));
            imageInput.files = dt.files;
        }, 100);
    }
}

    // =============================
    // ⭐ Thumbnail Switch (Optional)
    // =============================
    window.changeMainImage = function (src) {
        const mainImage = document.getElementById('mainImage');
        if (mainImage) {
            mainImage.style.opacity = 0;
            setTimeout(() => {
                mainImage.src = src;
                mainImage.style.opacity = 1;
            }, 150);
        }
    };

    // =============================
    // 🎥 Webcam Recording
    // =============================
    const recordBtn = document.getElementById('recordBtn');
    const stopBtn = document.getElementById('stopBtn');
    const videoPreview = document.getElementById('preview');
    let mediaRecorder, chunks = [];

    if (recordBtn && stopBtn && videoPreview) {
        recordBtn.addEventListener('click', async () => {
            try {
                const stream = await navigator.mediaDevices.getUserMedia({ video: true, audio: true });
                videoPreview.srcObject = stream;
                videoPreview.classList.remove('d-none');
                videoPreview.play();

                mediaRecorder = new MediaRecorder(stream);
                mediaRecorder.start();
                chunks = [];

                recordBtn.classList.add('d-none');
                stopBtn.classList.remove('d-none');

                mediaRecorder.ondataavailable = e => chunks.push(e.data);
                mediaRecorder.onstop = () => {
                    const blob = new Blob(chunks, { type: 'video/webm' });
                    const file = new File([blob], 'recorded_video.webm', { type: 'video/webm' });
                    const dt = new DataTransfer();
                    dt.items.add(file);
                    document.getElementById('video').files = dt.files;

                    stream.getTracks().forEach(track => track.stop());
                    videoPreview.srcObject = null;
                    videoPreview.src = URL.createObjectURL(blob);
                    videoPreview.load();
                };
            } catch (error) {
                alert('⚠️ Camera access denied or not supported!');
            }
        });

        stopBtn.addEventListener('click', () => {
            if (mediaRecorder && mediaRecorder.state === 'recording') {
                mediaRecorder.stop();
                recordBtn.classList.remove('d-none');
                stopBtn.classList.add('d-none');
            }
        });
    }

});
</script>


@endsection
