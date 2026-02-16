<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Display the Home Page with Working Image Carousel & Modern UI.
     */
    public function index()
    {
        // ✅ Correct image paths (using /storage/images instead of /images)
        $sliderImages = [
            [
                'url' => 'https://images.unsplash.com/photo-1481437156560-3205f6a55735?w=600&auto=format&fit=crop&q=60&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxzZWFyY2h8M3x8c2hvcHBpbmd8ZW58MHx8MHx8fDA%3D',
                'caption' => 'Upgrade Your Lifestyle',
                'subtext' => 'Discover premium collections tailored just for you.',
            ],
            [
                'url' => "https://images.unsplash.com/photo-1582004531564-50f300aae039?w=600&auto=format&fit=crop&q=60&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxzZWFyY2h8MTJ8fGVjb21tZXJjZXxlbnwwfHwwfHx8MA%3D%3D",
                'caption' => 'Exclusive Offers Await You',
                'subtext' => 'Grab the best deals of the season before they’re gone!',
            ],
            [
                'url' => "https://images.unsplash.com/photo-1555529669-e69e7aa0ba9a?w=600&auto=format&fit=crop&q=60&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxzZWFyY2h8NHx8c2hvcHBpbmd8ZW58MHx8MHx8fDA%3D",
                'caption' => 'Shop Smarter, Live Better',
                'subtext' => 'Quality products, fast delivery, and secure checkout.',
            ],
        ];

        // 🆕 Features Section
        $features = [
            [
                'icon' => '🚚',
                'title' => 'Free Shipping',
                'desc' => 'Free delivery on all orders above ₹499',
            ],
            [
                'icon' => '💳',
                'title' => 'Secure Payment',
                'desc' => 'Pay safely with trusted gateways & 256-bit encryption',
            ],
            [
                'icon' => '🛍️',
                'title' => 'Wide Range',
                'desc' => 'Explore thousands of categories and brands',
            ],
            [
                'icon' => '⭐',
                'title' => 'Top Rated Products',
                'desc' => 'Only the best products loved by our customers',
            ],
        ];

        return view('home.index', compact('sliderImages', 'features'));
    }
}
