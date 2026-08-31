<?php

use App\Http\Controllers\Admin\BrandController;
use App\Http\Controllers\Admin\CouponController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\CustomerController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\DeliveryZoneController;
use App\Http\Controllers\Admin\InventoryController;
use App\Http\Controllers\Admin\MediaController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\PageController;
use App\Http\Controllers\Admin\PermissionController;
use App\Http\Controllers\Admin\ProductAttributeController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\ProductImportExportController;
use App\Http\Controllers\Admin\ProfileController as AdminProfileController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\SiteSettingController;
use App\Http\Controllers\Admin\StockController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Api\EditorImageController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SeoController;
use Illuminate\Support\Facades\Route;

// Vue SPA - Routes with dynamic SEO meta tags
Route::get('/', [SeoController::class, 'handle'])->name('spa.home');
Route::get('/product/{slug}', [SeoController::class, 'handle'])->name('spa.product');
Route::get('/category/{slug}', [SeoController::class, 'handle'])->name('spa.category');
Route::get('/page/{slug}', [SeoController::class, 'handle'])->name('spa.page');

// Sitemap
Route::get('/sitemap.xml', \App\Http\Controllers\SitemapController::class)->name('sitemap');

// Robots.txt
Route::get('/robots.txt', fn() => response(file_get_contents(public_path('robots.txt')), 200, ['Content-Type' => 'text/plain']));

// Vue SPA - Catch all for client-side routing
Route::get('/{any}', [SeoController::class, 'handle'])->where('any', '^(?!admin|api).*$')->name('spa.catchall');

Route::get('/home', function () {
    return redirect()->route('admin.dashboard');
})->name('home');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::prefix(config('app.admin_prefix', 'admin'))->name('admin.')->group(function () {
        Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
        Route::get('/profile', [AdminProfileController::class, 'profile'])->name('profile');
        Route::get('/profile/edit', [AdminProfileController::class, 'editProfile'])->name('profile.edit');
        Route::patch('/profile', [AdminProfileController::class, 'updateProfile'])->name('profile.update');
        Route::post('/profile/password', [AdminProfileController::class, 'changePassword'])->name('profile.password');

        Route::resource('product/attribute', ProductAttributeController::class);
        Route::resource('brand', BrandController::class);
        Route::resource('category', CategoryController::class);

        // Product AJAX image upload routes
        Route::prefix('product')->name('product.')->group(function () {
            Route::post('/{product}/upload-thumbnail', [ProductController::class, 'uploadThumbnail'])->name('upload-thumbnail');
            Route::delete('/{product}/remove-thumbnail', [ProductController::class, 'removeThumbnail'])->name('remove-thumbnail');
            Route::post('/{product}/upload-gallery', [ProductController::class, 'uploadGallery'])->name('upload-gallery');
            Route::post('/{product}/delete-gallery', [ProductController::class, 'deleteGallery'])->name('delete-gallery');
            Route::post('/{product}/variant/upload-image', [ProductController::class, 'uploadVariantImage'])->name('upload-variant-image');
            Route::delete('/{product}/variant/delete-image', [ProductController::class, 'deleteVariantImage'])->name('delete-variant-image');
            Route::post('/{product}/variants/generate', [ProductController::class, 'generateVariants'])->name('variants-generate');
            Route::get('/{product}/variants', [ProductController::class, 'getVariants'])->name('variants-list');
            Route::delete('/{product}/variant', [ProductController::class, 'deleteVariant'])->name('variant-delete');
            Route::post('/{product}/variant/update', [ProductController::class, 'updateVariantField'])->name('variant-update');
        });
        Route::resource('product', ProductController::class);

        // Product Import / Export
        // XLSX export/template routes
        Route::get('product/export-xlsx', [ProductImportExportController::class, 'exportXlsx'])->name('product.export-xlsx');
        Route::get('product/template-xlsx', [ProductImportExportController::class, 'templateXlsx'])->name('product.template-xlsx');
        Route::get('product/import-export', [ProductImportExportController::class, 'index'])->name('product.import-export');
        Route::get('product/export', [ProductImportExportController::class, 'export'])->name('product.export');
        Route::post('product/import', [ProductImportExportController::class, 'import'])->name('product.import');
        Route::get('product/template', [ProductImportExportController::class, 'template'])->name('product.template');

        Route::get('ac_config_store', function () {
            $exitCode = Artisan::call('storage:link');
            return 'OK';
        });

        // Orders
        Route::prefix('orders')->name('orders.')->group(function () {
            Route::get('/', [OrderController::class, 'index'])->name('index');
            Route::get('/{order}', [OrderController::class, 'show'])->name('show');
            Route::put('/{order}', [OrderController::class, 'update'])->name('update');
            Route::delete('/{order}', [OrderController::class, 'destroy'])->name('destroy');
            Route::post('/{order}/update-status', [OrderController::class, 'updateStatus'])->name('update-status');
            Route::post('/{order}/update-payment', [OrderController::class, 'updatePayment'])->name('update-payment');
            Route::post('/{order}/update-shipping', [OrderController::class, 'updateShipping'])->name('update-shipping');
        });

        // Inventory
        Route::prefix('inventory')->name('inventory.')->group(function () {
            Route::get('/', [InventoryController::class, 'index'])->name('index');
            Route::post('/adjust', [InventoryController::class, 'adjust'])->name('adjust');
            Route::post('/stock-in', [InventoryController::class, 'stockIn'])->name('stock-in');
            Route::get('/warehouses/list', [InventoryController::class, 'warehouses'])->name('warehouses');
            Route::get('/warehouses/create', [InventoryController::class, 'createWarehouse'])->name('warehouse-create');
            Route::post('/warehouses', [InventoryController::class, 'storeWarehouse'])->name('warehouse-store');
            Route::get('/warehouses/{warehouse}/edit', [InventoryController::class, 'editWarehouse'])->name('warehouse-edit');
            Route::put('/warehouses/{warehouse}', [InventoryController::class, 'updateWarehouse'])->name('warehouse-update');
            Route::delete('/warehouses/{warehouse}', [InventoryController::class, 'destroyWarehouse'])->name('warehouse-destroy');
            Route::get('/stock-in', [InventoryController::class, 'stockInForm'])->name('stock-in-form');
            Route::get('/transfers', [InventoryController::class, 'transfers'])->name('transfers');
            Route::get('/transfers/create', [InventoryController::class, 'createTransfer'])->name('transfer-create');
            Route::post('/transfers', [InventoryController::class, 'storeTransfer'])->name('transfer-store');
            Route::post('/transfers/{transfer}/complete', [InventoryController::class, 'completeTransfer'])->name('transfer-complete');
            Route::post('/transfers/{transfer}/cancel', [InventoryController::class, 'cancelTransfer'])->name('transfer-cancel');
            Route::get('/ledger', [InventoryController::class, 'ledger'])->name('ledger');
            Route::get('/{inventory}', [InventoryController::class, 'show'])->name('show');
            Route::get('/{inventory}/adjust', [InventoryController::class, 'adjustForm'])->name('adjust-form');
        });

        // Stock management for variants
        Route::prefix('stock')->name('stock.')->group(function () {
            // Bulk stock adjustment
            Route::get('bulk-adjust', [StockController::class, 'bulkAdjustForm'])->name('bulk-adjust-form');
            Route::post('bulk-adjust', [StockController::class, 'bulkAdjust'])->name('bulk-adjust');

            Route::get('/', [StockController::class, 'index'])->name('index');
            Route::get('/{product}/stock-in', [StockController::class, 'stockInForm'])->name('stock-in-form');
            Route::post('/{product}/stock-in', [StockController::class, 'stockIn'])->name('stock-in');
            Route::get('/{product}/variant/{variant}', [StockController::class, 'show'])->name('variant-show');
        });

        // Delivery Zones
        Route::resource('delivery-zones', DeliveryZoneController::class);

        // Coupons
        Route::prefix('coupons')->name('coupons.')->group(function () {
            Route::get('/', [CouponController::class, 'index'])->name('index');
            Route::get('/create', [CouponController::class, 'create'])->name('create');
            Route::post('/', [CouponController::class, 'store'])->name('store');
            Route::get('/{coupon}', [CouponController::class, 'show'])->name('show');
            Route::get('/{coupon}/edit', [CouponController::class, 'edit'])->name('edit');
            Route::put('/{coupon}', [CouponController::class, 'update'])->name('update');
            Route::delete('/{coupon}', [CouponController::class, 'destroy'])->name('destroy');
            Route::post('/{coupon}/toggle-status', [CouponController::class, 'toggleStatus'])->name('toggle-status');
        });

        // Media Library
        Route::prefix('media')->name('media.')->group(function () {
            Route::get('/', [MediaController::class, 'index'])->name('index');
            Route::post('/', [MediaController::class, 'store'])->name('store');
            Route::post('/upload-ajax', [MediaController::class, 'uploadAjax'])->name('upload-ajax');
            Route::post('/browse', [MediaController::class, 'browse'])->name('browse');
            Route::get('/browse', [MediaController::class, 'browse'])->name('browse');
            Route::delete('/{medium}', [MediaController::class, 'destroy'])->name('destroy');
        });

        Route::prefix('pages')->name('pages.')->group(function () {
            Route::get('/', [PageController::class, 'index'])->name('index');
            Route::get('/create', [PageController::class, 'create'])->name('create');
            Route::post('/', [PageController::class, 'store'])->name('store');
            Route::post('/link-preview', [PageController::class, 'linkPreview'])->name('link-preview');
            Route::get('/{page}/edit', [PageController::class, 'edit'])->name('edit');
            Route::put('/{page}', [PageController::class, 'update'])->name('update');
            Route::delete('/{page}', [PageController::class, 'destroy'])->name('destroy');
        });

        Route::prefix('customers')->name('customers.')->group(function () {
            Route::get('/', [CustomerController::class, 'index'])->name('index');
            Route::get('/create', [CustomerController::class, 'create'])->name('create');
            Route::post('/', [CustomerController::class, 'store'])->name('store');
            Route::get('/{customer}', [CustomerController::class, 'show'])->name('show');
            Route::get('/{customer}/edit', [CustomerController::class, 'edit'])->name('edit');
            Route::put('/{customer}', [CustomerController::class, 'update'])->name('update');
            Route::delete('/{customer}', [CustomerController::class, 'destroy'])->name('destroy');
        });

        // Editor.js Image Upload (admin only)
        Route::post('/editor-images/upload', [EditorImageController::class, 'upload'])->name('editor-images.upload');
        Route::post('/editor-images/fetch-url', [EditorImageController::class, 'fetchUrl'])->name('editor-images.fetch-url');

        Route::prefix('settings')->name('settings.')->group(function () {
            // Users
            Route::get('/users', [UserController::class, 'index'])->name('users.index');
            Route::get('/users/create', [UserController::class, 'create'])->name('users.create');
            Route::post('/users', [UserController::class, 'store'])->name('users.store');
            Route::get('/users/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
            Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update');
            Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');
            // Roles
            Route::get('/roles', [RoleController::class, 'index'])->name('roles.index');
            Route::get('/roles/create', [RoleController::class, 'create'])->name('roles.create');
            Route::post('/roles', [RoleController::class, 'store'])->name('roles.store');
            Route::get('/roles/{role}/edit', [RoleController::class, 'edit'])->name('roles.edit');
            Route::put('/roles/{role}', [RoleController::class, 'update'])->name('roles.update');
            Route::delete('/roles/{role}', [RoleController::class, 'destroy'])->name('roles.destroy');
            // Permissions
            Route::get('/permissions', [PermissionController::class, 'index'])->name('permissions.index');
            Route::get('/permissions/create', [PermissionController::class, 'create'])->name('permissions.create');
            Route::post('/permissions', [PermissionController::class, 'store'])->name('permissions.store');
            Route::delete('/permissions/{permission}', [PermissionController::class, 'destroy'])->name('permissions.destroy');
            // Hero Slides
            Route::get('/hero-slides', [SiteSettingController::class, 'heroSlides'])->name('hero-slides');
            Route::post('/hero-slides', [SiteSettingController::class, 'storeHeroSlide'])->name('hero-slides.store');
            Route::put('/hero-slides/{slide}', [SiteSettingController::class, 'updateHeroSlide'])->name('hero-slides.update');
            Route::delete('/hero-slides/{slide}', [SiteSettingController::class, 'destroyHeroSlide'])->name('hero-slides.destroy');
            // Site Settings
            Route::get('/site-settings', [SiteSettingController::class, 'siteSettings'])->name('site-settings');
            Route::post('/site-settings', [SiteSettingController::class, 'updateSiteSettings'])->name('site-settings.update');
            // Feature Items
            Route::get('/feature-items', [SiteSettingController::class, 'featureItems'])->name('feature-items');
            Route::post('/feature-items', [SiteSettingController::class, 'storeFeatureItem'])->name('feature-items.store');
            Route::put('/feature-items/{index}', [SiteSettingController::class, 'updateFeatureItem'])->name('feature-items.update');
            Route::delete('/feature-items/{index}', [SiteSettingController::class, 'destroyFeatureItem'])->name('feature-items.destroy');

            // Theme Texts
            Route::get('/theme-texts', [SiteSettingController::class, 'themeTexts'])->name('theme-texts');
            Route::post('/theme-texts', [SiteSettingController::class, 'updateThemeTexts'])->name('theme-texts.update');
        });
    });
});

Route::prefix(config('app.admin_prefix', 'admin'))->group(function () {
    require __DIR__.'/auth.php';
});
