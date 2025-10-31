<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;

class HomeController extends Controller
{
    public function index()
    {
        $featuredProducts = Product::where('is_featured', true)->take(8)->get();
        $categories = Category::topLevel()->take(6)->get();

        return view('home.index', compact('featuredProducts', 'categories'));
    }
}
