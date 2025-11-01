@extends('layouts.app')

@section('title', 'Welcome to NewEcommerce')

@section('content')

<div class="container mx-auto mt-6">

    {{-- 🎠 Hero Carousel --}}
    <div id="homeCarousel"
         class="carousel slide carousel-fade shadow-2xl rounded-3xl overflow-hidden relative"
         data-bs-ride="carousel"
         data-bs-interval="3000">

        {{-- 🔘 Centered Carousel Indicators --}}
        <div class="absolute bottom-5 left-1/2 transform -translate-x-1/2 flex space-x-3 z-10">
            @foreach ($sliderImages as $index => $slide)
                <button type="button"
                        data-bs-target="#homeCarousel"
                        data-bs-slide-to="{{ $index }}"
                        class="w-3 h-3 rounded-full transition-all duration-300 {{ $index === 0 ? 'bg-white scale-125' : 'bg-gray-400 hover:bg-gray-300' }}"
                        aria-current="{{ $index === 0 ? 'true' : 'false' }}"
                        aria-label="Slide {{ $index + 1 }}">
                </button>
            @endforeach
        </div>

        {{-- 🖼️ Carousel Slides --}}
        <div class="carousel-inner">
            @foreach ($sliderImages as $index => $slide)
                <div class="carousel-item {{ $index === 0 ? 'active' : '' }} relative">
                    <img src="{{ $slide['url'] }}"
                         class="d-block w-100 hover:scale-105 transition-transform duration-700 ease-in-out"
                         alt="Slide {{ $index + 1 }}"
                         style="height: 550px; object-fit: cover;">

                    {{-- 🌈 Gradient Overlay --}}
                    <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent"></div>

                    {{-- ✨ Animated Captions --}}
                    <div class="carousel-caption absolute bottom-28 text-center">
                        <h2 class="text-white font-bold text-4xl md:text-5xl mb-3 animate__animated animate__fadeInDown">
                            {{ $slide['caption'] }}
                        </h2>
                        <p class="text-gray-200 text-lg mb-4 animate__animated animate__fadeInUp">
                            {{ $slide['subtext'] }}
                        </p>
                        <a href="{{ route('products.index') }}" 
                        class="btn btn-primary px-4 py-2 rounded-md text-white hover:bg-blue-700 transition">
                        🛒 Shop Now
                        </a>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- ◀️ ▶️ Controls --}}
        <button class="carousel-control-prev" type="button" data-bs-target="#homeCarousel" data-bs-slide="prev">
            <span class="carousel-control-prev-icon bg-gray-800 rounded-full p-2" aria-hidden="true"></span>
            <span class="visually-hidden">Previous</span>
        </button>

        <button class="carousel-control-next" type="button" data-bs-target="#homeCarousel" data-bs-slide="next">
            <span class="carousel-control-next-icon bg-gray-800 rounded-full p-2" aria-hidden="true"></span>
            <span class="visually-hidden">Next</span>
        </button>
    </div>

    {{-- ✨ Feature Section --}}
    <section class="mt-20 text-center">
        <h2 class="text-4xl font-bold text-gray-800 mb-10 tracking-tight">Why Shop With Us?</h2>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
            @foreach ($features as $feature)
                <div class="group bg-white shadow-lg hover:shadow-2xl transition transform hover:-translate-y-2 rounded-2xl p-8 flex flex-col items-center relative overflow-hidden">
                    <div class="absolute inset-0 bg-gradient-to-t from-blue-50/20 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition duration-700"></div>
                    <div class="text-6xl mb-3 transition-transform group-hover:scale-110">{{ $feature['icon'] }}</div>
                    <h3 class="text-xl font-semibold mb-2 text-gray-800">{{ $feature['title'] }}</h3>
                    <p class="text-gray-600 text-sm">{{ $feature['desc'] }}</p>
                </div>
            @endforeach
        </div>
    </section>

</div>

{{-- ✅ Auto Slide Script --}}
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const carousel = document.querySelector('#homeCarousel');
        if (carousel) {
            new bootstrap.Carousel(carousel, {
                interval: 3000,
                ride: 'carousel',
                pause: false,
                wrap: true
            });
        }
    });
</script>

@endsection
