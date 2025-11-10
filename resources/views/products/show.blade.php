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
<div class="mt-4" id="customer-reviews">


    <h4 class="fw-bold mb-4">
        <i class="bi bi-chat-left-text text-primary"></i> Customer Reviews
    </h4>

    @if($reviews->count() > 0)
        <div class="row g-4">
            @foreach($reviews as $review)
                <div class="col-12 col-md-6 col-lg-4">
                    <div class="border rounded-4 p-4 h-100 shadow-sm bg-white review-card">

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
                            <small class="text-muted">{{ $review->created_at->format('M d, Y') }}</small>
                        </div>

                        {{-- ✍️ Comment --}}
                        @if(!empty($review->comment))
                            <p class="text-secondary mb-2" style="font-size: 0.95rem;">
                                {{ $review->comment }}
                            </p>
                        @endif


                        <!-- Reviews Images -->
@php
    $reviewImages = [];

    if (!empty($review->images)) {
        // $review->images already has full URLs (handled in model)
        $reviewImages = is_array($review->images)
            ? $review->images
            : (array) $review->images;
    }
@endphp

@if(!empty($reviewImages))
    <div class="d-flex flex-wrap gap-3">
        @foreach($reviewImages as $imgUrl)
            <img 
                src="{{ $imgUrl }}" 
                alt="{{ $review->user->name ?? 'Review Image' }}" 
                class="img-fluid rounded border"
                style="width: 100px; height: 100px; object-fit: cover; background: #f9f9f9;"
            >
        @endforeach
    </div>
@endif
















                        {{-- 🎥 Review Video --}}
                        @if(!empty($review->video_path))
                            @php
                                $videoFilename = basename($review->video_path);
                                $storageVideoPath = 'reviews/videos/' . $videoFilename;
                                $videoUrl = Storage::disk('public')->exists($storageVideoPath)
                                    ? asset('storage/' . $storageVideoPath)
                                    : $review->video_path;
                            @endphp
                            <div class="mt-3">
                                <video controls class="rounded shadow-sm w-100" style="max-width: 100%;" preload="metadata">
                                    <source src="{{ $videoUrl }}" type="video/{{ pathinfo($videoFilename, PATHINFO_EXTENSION) }}">
                                    Your browser does not support HTML5 video.
                                </video>
                            </div>
                        @endif

                    </div>
                </div>
            @endforeach
        </div>

        {{-- ✅ Pagination --}}
        <div class="mt-4 d-flex justify-content-center">
            {{ $reviews->links('vendor.pagination.bootstrap-5') }}
        </div>
    @else
        <div class="alert alert-light border text-center">
            <p class="mb-0 text-muted">No reviews yet. Be the first to review this product!</p>
        </div>
    @endif
</div>

{{-- 🔹 Lightbox Modal --}}
<div class="modal fade" id="lightboxModal" tabindex="-1" aria-labelledby="lightboxModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content bg-transparent border-0">
            <div class="modal-body p-0 text-center">
                <img id="lightboxImage" src="" class="img-fluid rounded" alt="Review Image">
            </div>
            <div class="modal-footer border-0 justify-content-center">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>


{{-- 📝 Review Form --}}
@auth
<div class="mt-5">
    <h4 class="fw-bold mb-4 text-light">
        <i class="bi bi-star-half text-warning"></i> Write a Review
    </h4>

    {{-- ✅ Flash Messages --}}
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif
    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" enctype="multipart/form-data"
        action="{{ route('reviews.store', $product->id) }}"
        class="review-form p-4 rounded-4 shadow-lg border-0"
        style="background: linear-gradient(135deg, #1a1a2e, #16213e); color: #fff;">
        @csrf

        {{-- ⭐ Rating --}}
        <div class="mb-4">
            <label class="form-label fw-semibold text-white">Rating <span class="text-danger">*</span></label>
            <div class="star-rating d-flex flex-row-reverse justify-content-start">
                @for($i = 5; $i >= 1; $i--)
                    <input type="radio" name="rating" id="star{{ $i }}" value="{{ $i }}">
                    <label for="star{{ $i }}">★</label>
                @endfor
            </div>
            <p id="rating-text" class="mt-2 fw-semibold text-warning"></p>
        </div>

        {{-- 💬 Review Comment --}}
        <div class="mb-4">
            <label for="comment" class="form-label fw-semibold ">Your Review</label>
            <textarea name="comment" id="comment" rows="3" class="form-control bg-dark text-light border-0"
                      placeholder="Write your experience..." style="resize:none;"></textarea>
        </div>

        {{-- 📸 Drag & Drop Multiple Images --}}
        <div class="mb-4">
            <label class="form-label fw-semibold text-white">Upload Images (Drag & Drop)</label>
            <div id="dropArea" class="drop-zone text-center py-5 rounded-4 border-2 border-dashed border-light bg-dark"
                 style="cursor:pointer;">
                <i class="bi bi-cloud-upload display-5 text-light"></i>
                <p class="mt-2 text-light">Drag & drop or click to select multiple images</p>
                <input type="file" id="imageInput" name="images[]" multiple accept="image/jpeg,image/jpg,image/png,image/webp">
            </div>
            <div id="previewContainer" class="d-flex flex-wrap gap-3 mt-3"></div>
        </div>

        {{-- 🎥 Webcam Live Video Recording --}}
        <div class="mb-4 text-center">
            <label class="form-label fw-semibold text-white">Record Video (optional)</label><br>

            <video id="livePreview" autoplay muted playsinline
                class="rounded-3 border border-light mb-2"
                width="320" height="240" style="display:none;">
            </video>

            <video id="recordingPreview" controls
                class="rounded-3 border border-light mb-2"
                width="320" height="240" style="display:none;">
            </video>

            <div class="d-flex justify-content-center gap-2 mt-2">
                <button type="button" id="startRecording" class="btn btn-outline-success btn-sm">
                    Start Recording
                </button>
                <button type="button" id="stopRecording" class="btn btn-outline-danger btn-sm" disabled>
                    Stop Recording
                </button>
            </div>

            <input type="hidden" name="recorded_video_data" id="recordedVideoData">
        </div>

        {{-- 🎞️ Normal Video Upload --}}
        <div class="mb-4">
            <label class="form-label fw-semibold text-white">Upload Video (optional)</label>
            <input type="file" name="video" accept="video/mp4,video/quicktime"
                class="form-control bg-dark text-light border-0" id="videoUploadInput">
        </div>

        {{-- ✅ Submit --}}
        <button type="submit" class="btn btn-light px-4 py-2 fw-semibold shadow-sm">
            Submit Review
        </button>
    </form>
</div>
@else
<p class="text-muted mt-3">
    <a href="{{ route('login') }}" class="text-primary">Login</a> to post a review.
</p>
@endauth

</div>

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
/* Review Card Hover */
.review-card {
    transition: all 0.25s ease;
}
.review-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 4px 10px rgba(0,0,0,0.1);
}

/* Review Form */
.review-form {
    background: linear-gradient(135deg, #f0f6ff, #e8f0ff);
    color: #d6d62fff;
    transition: 0.3s;
    border-radius: 12px;
}
.review-form:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 10px rgba(0,0,0,0.08);
}

/* Star Rating */
.star-rating {
    display: inline-flex;
    flex-direction: row-reverse;
    font-size: 1.8rem;
    cursor: pointer;
}
.star-rating input {
    display: none;
}
.star-rating label {
    color: #d38686ff;
    transition: color 0.2s;
    cursor: pointer;
}
.star-rating input:checked ~ label,
.star-rating label:hover,
.star-rating label:hover ~ label {
    color: #ffb400;
}

/* Rating text */
#rating-text {
    margin-top: 8px;
    font-weight: 600;
    color: #ffb400;
}

/* Drag & Drop */
.drop-zone {
    border: 2px dashed #ddd;
    background: rgba(255, 255, 255, 0.15);
    cursor: pointer;
    transition: all 0.3s;
}
.drop-zone:hover {
    background: rgba(255, 255, 255, 0.25);
    border-color: #fff;
}

/* Image Previews */
.preview-image {
    position: relative;
    width: 110px;
    height: 110px;
    border-radius: 10px;
    overflow: hidden;
    border: 2px solid #fff;
    box-shadow: 0 2px 5px rgba(0,0,0,0.3);
}
.preview-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}
.remove-btn {
    position: absolute;
    top: 3px;
    right: 3px;
    background: rgba(255,0,0,0.8);
    color: white;
    border: none;
    border-radius: 50%;
    width: 22px;
    height: 22px;
    font-size: 14px;
    cursor: pointer;
    line-height: 1;
}
.remove-btn:hover {
    background: rgba(255,0,0,1);
}

/* Customer Review Cards */
#customer-reviews .border { border: 1px solid #e5e7eb !important; }
#customer-reviews h6 { font-size: 1rem; }
#customer-reviews video { border: 1px solid #dee2e6; }
#customer-reviews img:hover { transform: scale(1.05); transition: 0.3s ease-in-out; }

</style>

<script>
document.addEventListener("DOMContentLoaded", () => {
    // ⭐ STAR RATING LOGIC
    const ratingInputs = document.querySelectorAll(".star-rating input");
    const ratingText = document.getElementById("rating-text");

    const messages = {
        1: "Very Poor 😞",
        2: "Needs Improvement 😐",
        3: "Good 🙂",
        4: "Very Good 😄",
        5: "Excellent 🤩",
    };

    ratingInputs.forEach(input => {
        input.addEventListener("change", e => {
            ratingText.textContent = messages[e.target.value] || "";
        });
    });

    // Drag and Drop Logic
    const dropArea = document.getElementById("dropArea");
    const imageInput = document.getElementById("imageInput");
    const previewContainer = document.getElementById("previewContainer");

    let dt = new DataTransfer();

    ["dragenter","dragover"].forEach(event => {
        dropArea.addEventListener(event, e => {
            e.preventDefault();
            dropArea.classList.add("border-warning");
        });
    });

    ["dragleave","drop"].forEach(event => {
        dropArea.addEventListener(event, e => {
            e.preventDefault();
            dropArea.classList.remove("border-warning");
        });
    });

    dropArea.addEventListener("drop", e => {
        e.preventDefault();
        handleFiles(Array.from(e.dataTransfer.files));
    });

    dropArea.addEventListener("click", () => imageInput.click());
    imageInput.addEventListener("change", e => handleFiles(Array.from(e.target.files)));

    function handleFiles(newFiles) {
        newFiles.forEach(file => {
            if (!file.type.startsWith("image/")) {
                alert(`${file.name} is not a valid image file.`);
                return;
            }

            dt.items.add(file);

            const reader = new FileReader();
            reader.onload = e => {
                const div = document.createElement("div");
                div.className = "position-relative d-inline-block m-2";
                div.innerHTML = `
                    <img src="${e.target.result}" class="rounded-3 border border-light shadow-sm" width="100" height="100" style="object-fit:cover;">
                    <button type="button" class="btn btn-sm btn-danger position-absolute top-0 end-0 m-1 remove-btn">
                        <i class="bi bi-x-lg"></i>
                    </button>
                `;

                div.querySelector(".remove-btn").addEventListener("click", () => {
                    div.remove();
                    const updatedDt = new DataTransfer();
                    Array.from(dt.files)
                        .filter(f => f.name !== file.name)
                        .forEach(f => updatedDt.items.add(f));
                    dt = updatedDt;
                    imageInput.files = dt.files;
                });

                previewContainer.appendChild(div);
            };
            reader.readAsDataURL(file);
        });

        // ✅ Update input files once after all valid images
        imageInput.files = dt.files;
    }

    // 🎬 Live Webcam Recording
    const startRecBtn = document.getElementById("startRecording");
    const stopRecBtn = document.getElementById("stopRecording");
    const livePreview = document.getElementById("livePreview");
    const recordingPreview = document.getElementById("recordingPreview");
    const recordedVideoData = document.getElementById("recordedVideoData");

    let mediaRecorder, recordedChunks = [];

    startRecBtn.addEventListener("click", async () => {
        try {
            const stream = await navigator.mediaDevices.getUserMedia({ video:true, audio:true });
            livePreview.srcObject = stream;
            livePreview.style.display = "block";
            recordingPreview.style.display = "none";

            recordedChunks = [];
            mediaRecorder = new MediaRecorder(stream);

            mediaRecorder.ondataavailable = event => {
                if(event.data.size>0) recordedChunks.push(event.data);
            };

            mediaRecorder.onstop = () => {
                const blob = new Blob(recordedChunks, {type:"video/webm"});
                recordingPreview.src = URL.createObjectURL(blob);
                recordingPreview.style.display = "block";
                livePreview.style.display = "none";

                // Convert to Base64
                const reader = new FileReader();
                reader.onloadend = () => recordedVideoData.value = reader.result;
                reader.readAsDataURL(blob);
            };

            mediaRecorder.start();
            startRecBtn.disabled = true;
            stopRecBtn.disabled = false;
        } catch(err) {
            alert("Camera or microphone access denied!");
        }
    });

    stopRecBtn.addEventListener("click", () => {
        if(mediaRecorder && mediaRecorder.state !== "inactive") {
            mediaRecorder.stop();
            if(livePreview.srcObject) {
                livePreview.srcObject.getTracks().forEach(track => track.stop());
            }
            startRecBtn.disabled = false;
            stopRecBtn.disabled = true;
        }
    });

    // ✅ Stop webcam on page unload
    window.addEventListener("beforeunload", () => {
        if(livePreview.srcObject) {
            livePreview.srcObject.getTracks().forEach(track => track.stop());
        }
    });

    function openLightbox(url) {
    const img = document.getElementById('lightboxImage');
    img.src = url;



   
    
}

 function openLightbox(url) {
        const img = document.getElementById('lightboxImage');
        img.src = url;
    }

    // ✅ Related Images - Flipkart Style Gallery
    window.changeMainImage = function (newSrc, thumbEl) {
        const mainImage = document.getElementById('mainImage');
        
        // Smooth fade animation
        mainImage.style.opacity = 0;

        setTimeout(() => {
            mainImage.src = newSrc;
            mainImage.style.opacity = 1;
        }, 200);

        // Highlight selected thumbnail
        document.querySelectorAll('.gallery-thumb').forEach(img => {
            img.classList.remove('border-primary');
        });
        thumbEl.classList.add('border-primary');
    };
});


</script>


@endsection
