<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\CouponController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\PageController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
| All application web routes are defined here.
| Clean, organized, and grouped for clarity.
|--------------------------------------------------------------------------
*/

//
// =======================
// 🏠 FRONTEND ROUTES
// =======================
//

// 🏡 Home Page
Route::get('/', [HomeController::class, 'index'])->name('home');

// 📄 Static Pages
Route::get('/about', [PageController::class, 'about'])->name('about');
Route::get('/contact', [PageController::class, 'contact'])->name('contact');

// 🛍️ Products
Route::get('/products', [ProductController::class, 'index'])->name('products.index');
Route::get('/products/search', [ProductController::class, 'search'])->name('products.search');
Route::get('/products/{slug}', [ProductController::class, 'show'])->name('products.show');

// 🗂️ Category Browsing
Route::get('/category/{slug}', [CategoryController::class, 'show'])->name('category.show');



//
// =======================
// 👤 AUTHENTICATION ROUTES
// =======================
//

// 🧍 Register
Route::get('/register', [AuthController::class, 'showRegister'])->name('register.form');
Route::post('/register', [AuthController::class, 'register'])->name('register');

// 🔑 Login
Route::get('/login', [AuthController::class, 'showLogin'])->name('login.form');
Route::post('/login', [AuthController::class, 'login'])->name('login');

// 🚪 Logout
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// // 🔁 Forgot Password
// Route::get('/forgot-password', function () {
//     return view('auth.forgot-password');
// })->name('forgot.form');

Route::get('/forgot-password', [AuthController::class, 'showForgotForm'])->name('forgot.form');
Route::post('/forgot-password', [AuthController::class, 'sendResetLink'])->name('forgot.send');
Route::get('/reset-password/{token}', [AuthController::class, 'showResetForm'])->name('password.reset');
Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('password.update');

// 👤 Profile (Authenticated)
Route::middleware(['auth'])->group(function () {
    Route::get('/profile', [AuthController::class, 'profile'])->name('profile');
    Route::post('/profile/update', [AuthController::class, 'updateProfile'])->name('profile.update');
});



//
// =======================
// 🛒 CART & CHECKOUT ROUTES
// =======================
//
Route::middleware(['auth'])->group(function () {

    // 🛍️ Cart
    Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
    Route::post('/cart/add', [CartController::class, 'add'])->name('cart.add');
    Route::post('/cart/update/{id}', [CartController::class, 'update'])->name('cart.update');
    Route::delete('/cart/remove/{id}', [CartController::class, 'remove'])->name('cart.remove');
    Route::delete('/cart/clear', [CartController::class, 'clear'])->name('cart.clear');

    // 💳 Checkout
    Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
    Route::post('/checkout/apply-coupon', [CheckoutController::class, 'applyCoupon'])->name('checkout.applyCoupon');
    Route::post('/checkout/process', [CheckoutController::class, 'process'])->name('checkout.process');
    Route::post('/apply-coupon', [CouponController::class, 'apply'])->name('coupon.apply');
    Route::post('/remove-coupon', [CouponController::class, 'remove'])->name('coupon.remove');
    Route::post('/checkout/stripe-success', [CheckoutController::class, 'stripeSuccess'])->name('checkout.stripe.success');
    Route::get('/checkout/success', [CheckoutController::class, 'success'])->name('checkout.success');
    Route::get('/checkout/cancel', [CheckoutController::class, 'cancel'])->name('checkout.cancel');

    // 📦 Orders
    Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{id}', [OrderController::class, 'show'])->name('orders.show');

    // ⭐ Product Reviews
    Route::post('/product/{id}/review', [ReviewController::class, 'store'])->name('reviews.store');
});



//
// =======================
// 🧾 ADMIN PANEL ROUTES
// =======================
//
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {

    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');

    // 🛒 Manage Products
    Route::get('/products', [ProductController::class, 'adminIndex'])->name('products.index');
    Route::get('/products/create', [ProductController::class, 'create'])->name('products.create');
    Route::post('/products', [ProductController::class, 'store'])->name('products.store');
    Route::get('/products/{id}/edit', [ProductController::class, 'edit'])->name('products.edit');
    Route::put('/products/{id}', [ProductController::class, 'update'])->name('products.update');
    Route::delete('/products/{id}', [ProductController::class, 'destroy'])->name('products.destroy');

    // 🗂️ Manage Categories
    Route::get('/categories', [CategoryController::class, 'adminIndex'])->name('categories.index');
    Route::get('/categories/create', [CategoryController::class, 'create'])->name('categories.create');
    Route::post('/categories', [CategoryController::class, 'store'])->name('categories.store');
    Route::get('/categories/{id}/edit', [CategoryController::class, 'edit'])->name('categories.edit');
    Route::put('/categories/{id}', [CategoryController::class, 'update'])->name('categories.update');
    Route::delete('/categories/{id}', [CategoryController::class, 'destroy'])->name('categories.destroy');

    // 📦 Manage Orders
    Route::get('/orders', [OrderController::class, 'adminIndex'])->name('orders.index');
    Route::get('/orders/{id}', [OrderController::class, 'adminShow'])->name('orders.show');
    Route::put('/orders/{id}/status', [OrderController::class, 'updateStatus'])->name('orders.updateStatus');

    // 🎟️ Manage Coupons
    Route::get('/coupons', [CouponController::class, 'index'])->name('coupons.index');
    Route::post('/coupons', [CouponController::class, 'store'])->name('coupons.store');
    Route::delete('/coupons/{id}', [CouponController::class, 'destroy'])->name('coupons.destroy');
});



//
// =======================
// ⚠️ FALLBACK (404 PAGE)
// =======================
//
Route::fallback(function () {
    return response()->view('errors.404', [], 404);
});
