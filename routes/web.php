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
use App\Http\Controllers\PageController;
use App\Http\Controllers\WishlistController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\StripeWebhookController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\AdminOrderController;
use App\Http\Controllers\Admin\AdminUserController;
use App\Http\Controllers\Admin\AdminWishlistController;
use App\Http\Controllers\Admin\AdminProductController;
use App\Http\Controllers\Admin\AdminCouponController;
use App\Http\Controllers\Admin\AdminPaymentController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\WebRTCController;



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
Route::post('/contact', [PageController::class, 'sendContact'])->name('contact.send');

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


// // ⭐ Product Reviews
//     Route::post('/products/{product}/review', [App\Http\Controllers\ProductController::class, 'storeReview'])
//     ->name('products.review.store')
//     ->middleware('auth');

//     Route::post('/products/{id}/reviews', [App\Http\Controllers\ReviewController::class, 'store'])->name('reviews.store');

Route::post('/products/{id}/reviews', [App\Http\Controllers\ReviewController::class, 'store'])
    ->name('reviews.store')
    ->middleware('auth');




//
// =======================
// 🛒 CART & CHECKOUT ROUTES
// =======================
//
Route::middleware(['auth'])->group(function () {

    // Profiles
    Route::get('/profile', [AuthController::class, 'profile'])->name('profile');
    Route::post('/profile/update', [AuthController::class, 'updateProfile'])->name('profile.update');
    Route::post('/profile/avatar', [AuthController::class, 'updateAvatar'])->name('profile.avatar.update');

    // 🛍️ Cart
    Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
    Route::post('/cart/add', [CartController::class, 'add'])->name('cart.add');
    Route::delete('/cart/clear', [CartController::class, 'clear'])->name('cart.clear');

    // 💳 Checkout
    Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
    Route::post('/checkout/apply-coupon', [CheckoutController::class, 'applyCoupon'])->name('checkout.applyCoupon');
    Route::post('/checkout/process', [CheckoutController::class, 'process'])->name('checkout.process');
    Route::post('/remove-coupon', [CouponController::class, 'remove'])->name('coupon.remove');
    Route::post('/checkout/stripe-success', [CheckoutController::class, 'stripeSuccess'])->name('checkout.stripe.success');
    Route::get('/checkout/success', [CheckoutController::class, 'success'])->name('checkout.success');
    Route::get('/checkout/cancel', [CheckoutController::class, 'cancel'])->name('checkout.cancel');
    Route::post('/cart/{id}/update', [CartController::class, 'update'])->name('cart.update');
    Route::delete('/cart/remove/{id}', [CartController::class, 'remove'])->name('cart.remove');
    Route::post('/coupon/apply', [CartController::class, 'applyCoupon'])->name('coupon.apply');
    Route::post('/coupon/apply', [CouponController::class, 'apply'])->name('coupon.apply');
    Route::post('/coupon/remove', [CouponController::class, 'remove'])->name('coupon.remove');

    // 📦 Orders
    Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{id}', [OrderController::class, 'show'])->name('orders.show');


    

    Route::post('/stripe/webhook', [StripeWebhookController::class, 'handleWebhook']);
    // wishList
    Route::get('/wishlist', [WishlistController::class, 'index'])->name('wishlist.index');
    Route::post('/wishlist/add', [WishlistController::class, 'add'])->name('wishlist.add');
    Route::post('/wishlist/remove', [WishlistController::class, 'remove'])->name('wishlist.remove');

    // 🔔 Notifications
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::get('/notifications/unread', [NotificationController::class, 'getUnread'])->name('notifications.unread');
    Route::post('/notifications/{id}/read', [NotificationController::class, 'markAsRead'])->name('notifications.read');
    Route::post('/notifications/read-all', [NotificationController::class, 'markAllAsRead'])->name('notifications.readAll');

    // 📞 WebRTC Support Chat
    Route::get('/support-chat', [WebRTCController::class, 'index'])->name('support.chat');
    Route::post('/webrtc/signal', [WebRTCController::class, 'signal'])->name('webrtc.signal');
});


// Redirect /admin → /admin/dashboard
// Route::get('/admin', function () {
//     return redirect()->route('admin.dashboard');
// });

//
// ===============================
// 🏠 ADMIN DASHBOARD ROUTES
// ===============================
Route::middleware(['web', 'auth', 'admin'])
    ->prefix('admin')
    ->as('admin.')
    ->group(function () {
        Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');;
    });


//
// ===============================
// 👤 ADMIN PROFILE ROUTES
// ===============================
Route::middleware(['web', 'auth', 'admin'])
    ->prefix('admin/profile')
    ->as('admin.profile.')
    ->group(function () {
        Route::get('/', [AdminController::class, 'profile'])->name('index');
        Route::get('/edit', [AdminController::class, 'editProfile'])->name('edit');
        Route::put('/update', [AdminController::class, 'updateProfile'])->name('update');
    });


//
// ===============================
// 🛍️ ADMIN PRODUCT ROUTES
// ===============================
// Route::middleware(['web', 'auth', 'admin'])
//     ->prefix('admin/products')
//     ->as('admin.products.')
//     ->group(function () {
//         Route::get('/', [AdminProductController::class, 'adminIndex'])->name('index');
//         Route::get('/create', [AdminProductController::class, 'create'])->name('create');
//         Route::post('/', [AdminProductController::class, 'store'])->name('store');
//         Route::get('/{id}/edit', [AdminProductController::class, 'edit'])->name('edit');
//         Route::put('/{id}', [AdminProductController::class, 'update'])->name('update');
//         Route::delete('/{id}', [AdminProductController::class, 'destroy'])->name('destroy');
//     });


Route::middleware(['web', 'auth', 'admin'])
    ->prefix('admin/products')
    ->as('admin.products.')
    ->group(function () {
        Route::get('/', [AdminProductController::class, 'adminIndex'])->name('index');
        Route::get('/create', [AdminProductController::class, 'create'])->name('create');
        Route::post('/', [AdminProductController::class, 'store'])->name('store');
        Route::get('/{product}/edit', [AdminProductController::class, 'edit'])->name('edit'); // ✅ FIXED
        Route::put('/{product}', [AdminProductController::class, 'update'])->name('update'); // ✅ FIXED
        Route::delete('/{product}', [AdminProductController::class, 'destroy'])->name('destroy'); // ✅ FIXED
    });

//
// ===============================
// 📦 ADMIN CATEGORY ROUTES
// ===============================
Route::middleware(['web', 'auth', 'admin'])
    ->prefix('admin/categories')
    ->as('admin.categories.')
    ->group(function () {
        Route::get('/', [CategoryController::class, 'adminIndex'])->name('index');
        Route::get('/create', [CategoryController::class, 'create'])->name('create');
        Route::post('/', [CategoryController::class, 'store'])->name('store');
        Route::get('/{id}/edit', [CategoryController::class, 'edit'])->name('edit');
        Route::put('/{id}', [CategoryController::class, 'update'])->name('update');
        Route::delete('/{category}', [CategoryController::class, 'destroy'])->name('destroy');
    });


//
// ===============================
// 🧾 ADMIN ORDER ROUTES
// ===============================
Route::middleware(['web', 'auth', 'admin'])
    ->prefix('admin/orders')
    ->as('admin.orders.')
    ->group(function () {
        Route::get('/', [AdminOrderController::class, 'adminIndex'])->name('index');
        Route::get('/{id}', [AdminOrderController::class, 'adminShow'])->name('show');
        Route::put('/{id}/status', [AdminOrderController::class, 'updateStatus'])->name('updateStatus');
    });


//
// ===============================
// 🎟️ ADMIN COUPON ROUTES
// ===============================
Route::middleware(['web', 'auth', 'admin'])
    ->prefix('admin/coupons')
    ->as('admin.coupons.')
    ->group(function () {
        Route::get('/', [AdminCouponController::class, 'index'])->name('index');
        Route::get('/create', [AdminCouponController::class, 'create'])->name('create');
        Route::post('/', [AdminCouponController::class, 'store'])->name('store');
        Route::get('/{id}/edit', [AdminCouponController::class, 'edit'])->name('edit');
        Route::put('/{id}', [AdminCouponController::class, 'update'])->name('update');
        Route::delete('/{id}', [AdminCouponController::class, 'destroy'])->name('destroy');
    });


//
// ===============================
// 💖 ADMIN WISHLIST ROUTES
// ===============================
Route::middleware(['web', 'auth', 'admin'])
    ->prefix('admin/wishlist')
    ->as('admin.wishlist.')
    ->group(function () {
        Route::get('/', [AdminWishlistController::class, 'index'])->name('index');
        Route::delete('/{id}', [AdminWishlistController::class, 'destroy'])->name('destroy');
    });


//
// ===============================
// 💳 ADMIN PAYMENT ROUTES
// ===============================
Route::middleware(['web', 'auth', 'admin'])
    ->prefix('admin/payments')
    ->as('admin.payments.')
    ->group(function () {
        Route::get('/', [AdminPaymentController::class, 'index'])->name('index');
        Route::get('/{id}', [AdminPaymentController::class, 'show'])->name('show');
        Route::put('/{id}/status', [AdminPaymentController::class, 'updateStatus'])->name('updateStatus');
    });


//
// ===============================
// 👥 ADMIN USER ROUTES
// ===============================
Route::middleware(['web', 'auth', 'admin'])
    ->prefix('admin/users')
    ->as('admin.users.')
    ->group(function () {
        Route::get('/', [AdminUserController::class, 'index'])->name('index');
        Route::get('/create', [AdminUserController::class, 'create'])->name('create');
        Route::post('/', [AdminUserController::class, 'store'])->name('store');
        Route::get('/{id}', [AdminUserController::class, 'show'])->name('show');
        Route::delete('/{id}', [AdminUserController::class, 'destroy'])->name('destroy');
    });




//
// =======================
// ⚠️ FALLBACK (404 PAGE)
// =======================
//
Route::fallback(function () {
    return response()->view('errors.404', [], 404);
});
