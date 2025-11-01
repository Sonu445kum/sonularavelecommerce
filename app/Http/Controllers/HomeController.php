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
                'url' => asset('storage/images/banner1.jpg'),
                'caption' => 'Upgrade Your Lifestyle',
                'subtext' => 'Discover premium collections tailored just for you.',
            ],
            [
                'url' => asset('storage/images/baaner2.jpg'),
                'caption' => 'Exclusive Offers Await You',
                'subtext' => 'Grab the best deals of the season before they’re gone!',
            ],
            [
                'url' => asset('storage/images/baaner3.jpg'),
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
