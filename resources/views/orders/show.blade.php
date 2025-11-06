@extends('layouts.app')

@section('title', 'Order Details')

@section('content')
<div class="container mx-auto px-4 py-10">
    <div class="bg-white rounded-xl shadow-md p-6">

        {{-- 🧾 Order Header --}}
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">
                    Order #{{ $order->order_number ?? $order->id }}
                </h1>
                <p class="text-gray-500 text-sm">
                    Placed on {{ $order->created_at->format('d M Y, h:i A') }}
                </p>
            </div>

            {{-- 🔖 Status Badge --}}
            <span class="mt-3 sm:mt-0 px-3 py-1 text-sm font-semibold rounded-full 
                @if($order->status === 'Pending') bg-yellow-100 text-yellow-800
                @elseif($order->status === 'Processing') bg-blue-100 text-blue-800
                @elseif($order->status === 'Delivered') bg-green-100 text-green-800
                @elseif($order->status === 'Cancelled') bg-red-100 text-red-800
                @else bg-gray-100 text-gray-800
                @endif">
                {{ ucfirst($order->status ?? 'Pending') }}
            </span>
        </div>

        {{-- 📦 Shipping Address --}}
        <div class="border rounded-lg p-4 mb-6 bg-gray-50">
            <h2 class="font-semibold text-gray-800 mb-3">Shipping Address</h2>
            @php $address = $order->address ?? null; @endphp

            @if(is_object($address))
                <div class="space-y-1 text-gray-700 leading-relaxed">
                    <p class="font-semibold text-lg">{{ $address->name ?? 'N/A' }}</p>
                    <p>📞 {{ $address->phone ?? 'N/A' }}</p>
                    <p>{{ $address->address_line1 ?? $address->address ?? 'N/A' }}</p>
                    @if(!empty($address->address_line2))
                        <p>{{ $address->address_line2 }}</p>
                    @endif
                    <p>{{ $address->city ?? '' }}, {{ $address->state ?? '' }} - {{ $address->postal_code ?? $address->pincode ?? '' }}</p>
                    <p>{{ $address->country ?? 'India' }}</p>

                    @if(!empty($address->label))
                        <span class="inline-block mt-2 text-xs px-2 py-1 bg-indigo-100 text-indigo-800 rounded-md">
                            {{ ucfirst($address->label) }}
                        </span>
                    @endif
                </div>
            @else
                <div class="space-y-1 text-gray-700 leading-relaxed">
                    <p class="font-semibold text-lg">{{ $order->name ?? 'N/A' }}</p>
                    <p>📞 {{ $order->phone ?? 'N/A' }}</p>
                    <p>{{ $order->address ?? 'N/A' }}</p>
                    <p>{{ $order->city ?? '' }}, {{ $order->state ?? '' }} - {{ $order->pincode ?? '' }}</p>
                    <p>{{ $order->country ?? 'India' }}</p>
                </div>
            @endif
        </div>

        {{-- 🛍️ Order Items --}}
        <h2 class="font-semibold text-gray-800 mb-3">Order Items</h2>
        <div class="divide-y">
            @foreach($order->items as $item)
                @php
                    $product = $item->product ?? null;
                    $imagePath = $item->product_image
                        ?? optional($product)->featured_image
                        ?? optional($product?->images?->first())->path
                        ?? 'images/default-product.jpg';
                    $imageUrl = str_starts_with($imagePath, 'http')
                        ? $imagePath
                        : asset('storage/' . ltrim($imagePath, '/'));
                    $productName = $item->product_name
                        ?? optional($product)->title
                        ?? optional($product)->name
                        ?? 'Product';
                    $existingReview = $product ? $product->reviews->where('user_id', auth()->id())->first() : null;
                @endphp

                <div class="py-4">
                    <div class="flex justify-between items-center">
                        <div class="flex items-center space-x-4">
                            <img src="{{ $imageUrl }}"
                                 alt="{{ $productName }}"
                                 class="w-16 h-16 rounded-md object-cover border"
                                 onerror="this.src='{{ asset('images/default-product.jpg') }}'">

                            <div>
                                <p class="font-semibold text-gray-800">{{ $productName }}</p>
                                <p class="text-gray-500 text-sm">Qty: {{ $item->quantity }}</p>
                            </div>
                        </div>

                        <p class="font-semibold text-gray-800">
                            ₹{{ number_format($item->unit_price * $item->quantity, 2) }}
                        </p>
                    </div>

                    {{-- ⭐ Delivered Orders: Review Section --}}
                    @if($order->status === 'Delivered' && $product)
                        <div class="mt-4 w-full bg-white border rounded-lg p-4 shadow-sm">
                            <h3 class="font-semibold text-gray-900 text-lg mb-3">Your Review</h3>

                            @if($existingReview)
                                {{-- ✅ Already submitted review --}}
                                <div class="border rounded p-3 bg-gray-50">
                                    <div class="flex items-center mb-2">
                                        @for($i=1;$i<=5;$i++)
                                            <svg class="w-5 h-5 {{ $i <= $existingReview->rating ? 'text-yellow-400' : 'text-gray-300' }}" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.286 3.975a1 1 0 00.95.69h4.18c.969 0 1.371 1.24.588 1.81l-3.385 2.46a1 1 0 00-.364 1.118l1.287 3.974c.3.922-.755 1.688-1.54 1.118l-3.385-2.46a1 1 0 00-1.175 0l-3.385 2.46c-.785.57-1.84-.196-1.54-1.118l1.287-3.974a1 1 0 00-.364-1.118L2.045 9.402c-.783-.57-.38-1.81.588-1.81h4.18a1 1 0 00.95-.69l1.286-3.975z"/>
                                            </svg>
                                        @endfor
                                    </div>
                                    <p>{{ $existingReview->comment }}</p>

                                    {{-- Show images/video if uploaded --}}
                                    @if($existingReview->images)
                                        <div class="flex gap-2 mt-2 flex-wrap">
                                            @foreach($existingReview->images as $img)
                                                <img src="{{ asset('storage/' . $img) }}" class="w-16 h-16 rounded-md object-cover border">
                                            @endforeach
                                        </div>
                                    @endif
                                    @if($existingReview->video)
                                        <video src="{{ asset('storage/' . $existingReview->video) }}" controls class="mt-2 w-full max-w-md rounded-md"></video>
                                    @endif
                                </div>
                            @else
                                {{-- ✍️ Review Form --}}
                                <form action="{{ route('reviews.store', $product->id) }}" method="POST" enctype="multipart/form-data" class="space-y-3">
                                    @csrf
                                    <input type="hidden" name="rating" id="rating-input-{{ $item->id }}">
                                    
                                    {{-- 🟡 Star Rating --}}
                                    <div class="flex items-center mb-2" id="rating-{{ $item->id }}">
                                        @for($i=1;$i<=5;$i++)
                                            <svg class="w-7 h-7 cursor-pointer text-gray-300 hover:text-yellow-400"
                                                 fill="currentColor"
                                                 onclick="setRating({{ $item->id }}, {{ $i }})"
                                                 id="star-{{ $item->id }}-{{ $i }}"
                                                 xmlns="http://www.w3.org/2000/svg"
                                                 viewBox="0 0 20 20">
                                                 <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.286 3.975a1 1 0 00.95.69h4.18c.969 0 1.371 1.24.588 1.81l-3.385 2.46a1 1 0 00-.364 1.118l1.287 3.974c.3.922-.755 1.688-1.54 1.118l-3.385-2.46a1 1 0 00-1.175 0l-3.385 2.46c-.785.57-1.84-.196-1.54-1.118l1.287-3.974a1 1 0 00-.364-1.118L2.045 9.402c-.783-.57-.38-1.81.588-1.81h4.18a1 1 0 00.95-.69l1.286-3.975z"/>
                                            </svg>
                                        @endfor
                                    </div>

                                    <textarea name="comment" rows="3" class="w-full border rounded-md p-2 focus:ring-2 focus:ring-yellow-400 text-gray-900" placeholder="Share your experience with this product..."></textarea>
                                    
                                    {{-- 🖼 Drag & Drop Images --}}
                                    <div>
                                        <label class="block text-gray-800 font-medium mb-1">Upload Images</label>
                                        <div class="border-2 border-dashed border-gray-300 rounded-md p-4 text-center cursor-pointer hover:border-yellow-400 relative"
                                             id="drop-zone-{{ $item->id }}">
                                            Drag & Drop images here or click to select
                                            <input type="file" name="images[]" multiple accept="image/*"
                                                   class="absolute inset-0 w-full h-full opacity-0 cursor-pointer"
                                                   onchange="handleFiles(event, {{ $item->id }})">
                                        </div>
                                        <div id="image-preview-{{ $item->id }}" class="flex gap-2 mt-2 flex-wrap"></div>
                                    </div>

                                    {{-- 🎥 Video Upload --}}
                                    <div>
                                        <label class="block text-gray-800 font-medium mb-1">Upload Video (optional)</label>
                                        <input type="file" name="video" accept="video/*" class="block w-full text-sm text-gray-500" onchange="previewVideo(event, {{ $item->id }})">
                                        <video id="video-preview-{{ $item->id }}" class="mt-2 w-full max-w-md rounded-md hidden" controls></video>
                                    </div>

                                    <button type="submit" class="bg-yellow-500 text-white px-4 py-2 rounded-md hover:bg-yellow-600 transition">
                                        Submit Review
                                    </button>
                                </form>
                            @endif
                        </div>
                    @endif
                </div>
            @endforeach
        </div>

        {{-- 💰 Price Summary --}}
        <div class="border-t mt-6 pt-4 text-gray-700">
            <div class="flex justify-between py-1">
                <span>Subtotal</span>
                <span>₹{{ number_format($order->subtotal ?? 0, 2) }}</span>
            </div>
            @if($order->discount > 0)
                <div class="flex justify-between py-1 text-green-700">
                    <span>Discount ({{ $order->coupon_code ?? 'Coupon' }})</span>
                    <span>-₹{{ number_format($order->discount, 2) }}</span>
                </div>
            @endif
            <div class="flex justify-between py-1">
                <span>Shipping</span>
                <span>₹{{ number_format($order->shipping ?? 50, 2) }}</span>
            </div>
            <div class="flex justify-between py-1 font-bold text-gray-900 text-lg">
                <span>Total</span>
                <span>₹{{ number_format($order->total ?? ($order->subtotal + ($order->shipping ?? 50)), 2) }}</span>
            </div>
        </div>

        {{-- 🔙 Footer --}}
        <div class="mt-8 text-right">
            <a href="{{ route('orders.index') }}" class="inline-block bg-gray-200 text-gray-800 px-4 py-2 rounded-md hover:bg-gray-300 transition text-sm">
                ← Back to Orders
            </a>
        </div>
    </div>
</div>

{{-- ⭐ JS for Rating, Drag & Drop, Video Preview --}}
<script>
function setRating(itemId, rating) {
    document.getElementById(`rating-input-${itemId}`).value = rating;
    for (let i=1;i<=5;i++){
        const star=document.getElementById(`star-${itemId}-${i}`);
        star.classList.toggle('text-yellow-400', i <= rating);
        star.classList.toggle('text-gray-300', i > rating);
    }
}

function handleFiles(event, itemId){
    const container = document.getElementById(`image-preview-${itemId}`);
    const files = event.target.files || event.dataTransfer?.files;
    if (!files) return;
    container.innerHTML='';
    Array.from(files).forEach(file=>{
        const reader = new FileReader();
        reader.onload = e=>{
            const div=document.createElement('div');
            div.className='relative';
            const img=document.createElement('img');
            img.src=e.target.result;
            img.className='w-16 h-16 object-cover rounded-md border';
            const del=document.createElement('span');
            del.innerHTML='&times;';
            del.className='absolute top-0 right-0 bg-red-500 text-white rounded-full w-5 h-5 flex items-center justify-center cursor-pointer';
            del.onclick=()=> div.remove();
            div.appendChild(img);
            div.appendChild(del);
            container.appendChild(div);
        };
        reader.readAsDataURL(file);
    });
}

function previewVideo(event, itemId){
    const video=document.getElementById(`video-preview-${itemId}`);
    const file=event.target.files[0];
    if(file){
        video.src=URL.createObjectURL(file);
        video.classList.remove('hidden');
    }else{
        video.src='';
        video.classList.add('hidden');
    }
}

@foreach($order->items as $item)
const dropZone{{ $item->id }}=document.getElementById('drop-zone-{{ $item->id }}');
dropZone{{ $item->id }}.addEventListener('dragover', e=>{e.preventDefault(); dropZone{{ $item->id }}.classList.add('border-yellow-400');});
dropZone{{ $item->id }}.addEventListener('dragleave', e=>{dropZone{{ $item->id }}.classList.remove('border-yellow-400');});
dropZone{{ $item->id }}.addEventListener('drop', e=>{
    e.preventDefault(); dropZone{{ $item->id }}.classList.remove('border-yellow-400');
    const input=dropZone{{ $item->id }}.querySelector('input[type="file"]');
    input.files=e.dataTransfer.files;
    handleFiles({target:input}, {{ $item->id }});
});
@endforeach
</script>
@endsection
