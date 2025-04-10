<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\ShopController;
use App\Models\Post;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;


Route::get('/', [ShopController::class, 'store_front'])
    ->name('shop.store_front')
    ->lazy();
Route::get('/catfilter', [ShopController::class, 'cat_filter'])
    ->name('shop.cat_filter');
Route::get('/product/{id}', [ShopController::class, 'product_overview'])
    ->name('shop.product_overview');
Route::get('/checkout', [ShopController::class, 'checkout'])
    ->name('shop.checkout');

// Blog routes
Route::get('/posts', [ShopController::class, 'posts_list'])
    ->name('posts.index');

Route::get('/posts/{id}', [ShopController::class, 'post_detail'])
    ->name('posts.show');

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
Route::get('/download-nhap-hang-report', [AdminController::class, 'download_nhap_hang_report'])->name('admin.download_nhap_hang_report');

Route::get('/run-storage-link', function () {
    try {
        Artisan::call('storage:link');
        return response()->json(['message' => 'Storage linked successfully!'], 200);
    } catch (\Exception $e) {
        return response()->json(['error' => $e->getMessage()], 500);
    }
});

// Product Image Organizer Routes
Route::get('/admin/products/{product}/images/organize', [App\Http\Controllers\ProductImageOrganizerController::class, 'index'])->name('product.images.organize');
Route::post('/admin/products/{product}/images/update-order', [App\Http\Controllers\ProductImageOrganizerController::class, 'updateOrder'])->name('product.images.update-order');
Route::post('/admin/products/{product}/images/reset-order', [App\Http\Controllers\ProductImageOrganizerController::class, 'resetOrder'])->name('product.images.reset-order');
