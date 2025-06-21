<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ShopController;
use App\Models\Post;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;
use App\Http\Controllers\SitemapController;


Route::get('/', [ShopController::class, 'store_front'])
    ->name('shop.store_front')
    ->lazy();
Route::get('/catfilter', [ShopController::class, 'cat_filter'])
    ->name('shop.cat_filter');
Route::get('/product/{slug}', [ShopController::class, 'product_overview'])
    ->name('shop.product_overview')
    ->where('slug', '[a-z0-9-]+');
Route::get('/checkout', [ShopController::class, 'checkout'])
    ->name('shop.checkout');

// Authentication routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});

// Blog routes
Route::get('/posts', [ShopController::class, 'posts_list'])
    ->name('posts.index');

Route::get('/posts/{id}', [ShopController::class, 'post_detail'])
    ->name('posts.show');

// Admin routes - require authentication
Route::middleware('auth')->group(function () {
    // xử lý nhập liệu
    Route::get('/excel', [AdminController::class, 'excel'])
        ->name('shop.excel');
    Route::get('/form_import_excel', [AdminController::class, 'form_import_excel']);
    Route::post('/import_excel', [AdminController::class, 'import_excel'])
        ->name('shop.import_excel');

    // Thêm route mới cho form nhập hàng
    Route::get('/tq', [AdminController::class, 'form_nhap_hang'])
        ->name('admin.form_nhap_hang');
    Route::post('/nhap_hang', [AdminController::class, 'nhap_hang'])
        ->name('admin.nhap_hang');

    // Import hàng
    Route::get('/form-nhap-hang', [AdminController::class, 'form_nhap_hang'])->name('admin.form_nhap_hang');
    Route::post('/nhap-hang', [AdminController::class, 'nhap_hang'])->name('admin.nhap_hang');
    Route::get('/g-repordownload-nhap-hant', [AdminController::class, 'download_nhap_hang_report'])->name('admin.download_nhap_hang_report');

    // Admin route to download Sapo file
    Route::get('/admin/download_nhap_hang_sapo', [AdminController::class, 'download_nhap_hang_sapo'])->name('admin.download_nhap_hang_sapo');

    // Product Image Organizer Routes
    Route::get('/admin/products/{product}/images/organize', [App\Http\Controllers\ProductImageOrganizerController::class, 'index'])->name('product.images.organize');
    Route::post('/admin/products/{product}/images/update-order', [App\Http\Controllers\ProductImageOrganizerController::class, 'updateOrder'])->name('product.images.update-order');
    Route::post('/admin/products/{product}/images/reset-order', [App\Http\Controllers\ProductImageOrganizerController::class, 'resetOrder'])->name('product.images.reset-order');
});

Route::get('/run-storage-link', function () {
    try {
        Artisan::call('storage:link');
        return response()->json(['message' => 'Storage linked successfully!'], 200);
    } catch (\Exception $e) {
        return response()->json(['error' => $e->getMessage()], 500);
    }
});

// Sitemap route
Route::get('sitemap.xml', [SitemapController::class, 'index']);
