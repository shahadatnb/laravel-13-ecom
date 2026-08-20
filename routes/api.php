<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\CouponController;
use App\Http\Controllers\Api\DeliveryZoneController;
use App\Http\Controllers\Api\ModuleController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\PageController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\SiteSettingController;
use App\Http\Controllers\Api\WishlistController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// Public routes
Route::get('/', function () {
    return response()->json([
        'message' => 'Enterprise Ecommerce ERP API',
        'version' => '1.0.0',
    ]);
});

// Product routes (public)
Route::prefix('products')->group(function () {
    Route::get('/', [ProductController::class, 'index'])->name('products.index');
    Route::get('/featured', [ProductController::class, 'featured'])->name('products.featured');
    Route::get('/new-arrivals', [ProductController::class, 'newArrivals'])->name('products.new-arrivals');
    Route::get('/search', [ProductController::class, 'search'])->name('products.search');
    Route::get('/{slug}', [ProductController::class, 'show'])->name('products.show');
});

// Delivery zones (public)
Route::prefix('delivery-zones')->group(function () {
    Route::get('/', [DeliveryZoneController::class, 'index'])->name('delivery-zones.index');
    Route::get('/districts', [DeliveryZoneController::class, 'districts'])->name('delivery-zones.districts');
    Route::get('/calculate', [DeliveryZoneController::class, 'calculate'])->name('delivery-zones.calculate');
});

// Site settings (public)
Route::get('/site-settings', [SiteSettingController::class, 'index'])->name('site-settings');

// Feature modules for the current store
Route::get('/modules', [ModuleController::class, 'index'])->name('modules');

// Dynamic pages (public)
Route::prefix('pages')->group(function () {
    Route::get('/', [PageController::class, 'index'])->name('pages.index');
    Route::get('/{slug}', [PageController::class, 'show'])->name('pages.show');
});

// Auth routes (public)
Route::prefix('auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register'])->name('auth.register');
    Route::post('/login', [AuthController::class, 'login'])->name('auth.login');
});

// Protected auth routes
Route::middleware('auth:customer')->group(function () {
    Route::post('/auth/logout', [AuthController::class, 'logout'])->name('auth.logout');
    Route::get('/customer', [AuthController::class, 'user'])->name('auth.user');
});

// Category routes (public)
Route::prefix('categories')->group(function () {
    Route::get('/', [CategoryController::class, 'index'])->name('categories.index');
    Route::get('/active', [CategoryController::class, 'active'])->name('categories.active');
    Route::get('/{slug}', [CategoryController::class, 'show'])->name('categories.show');
});

// Public route for guest checkout
Route::post('/guest-orders', [OrderController::class, 'store'])->name('orders.guest-store');

// Public order tracking
Route::get('/orders/track', [OrderController::class, 'trackByOrderNumber'])->name('orders.track');

// Customer routes
Route::middleware('auth:customer')->group(function () {
    Route::prefix('orders')->group(function () {
        Route::get('/', [OrderController::class, 'index'])->name('orders.index');
        Route::post('/', [OrderController::class, 'store'])->name('orders.store');
        Route::get('/{order}', [OrderController::class, 'show'])->name('orders.show');
        Route::post('/{order}/cancel', [OrderController::class, 'cancel'])->name('orders.cancel');
        Route::get('/{order}/tracking', [OrderController::class, 'tracking'])->name('orders.tracking');
    });

    // Coupon routes for customers
    Route::prefix('coupons')->group(function () {
        Route::get('/available', [CouponController::class, 'available'])->name('coupons.available');
        Route::post('/validate', [CouponController::class, 'checkCoupon'])->name('coupons.validate');
    });

    // Wishlist routes
    Route::prefix('wishlist')->group(function () {
        Route::get('/', [WishlistController::class, 'index'])->name('wishlist.index');
        Route::post('/', [WishlistController::class, 'store'])->name('wishlist.store');
        Route::post('/check', [WishlistController::class, 'check'])->name('wishlist.check');
        Route::delete('/{wishlistItem}', [WishlistController::class, 'destroy'])->name('wishlist.destroy');
    });
});
