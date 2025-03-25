<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\ShopController;
use App\Models\Product;
use Illuminate\Support\Facades\Route;


Route::get('/', [ShopController::class, 'store_front'])->name('shop.store_front')->lazy();
Route::get('/catfilter', [ShopController::class, 'cat_filter'])->name('shop.cat_filter');
Route::get('/product/{id}', [ShopController::class, 'product_overview'])->name('shop.product_overview');
Route::get('/checkout', [ShopController::class, 'checkout'])->name('shop.checkout');

// xử lý nhập liệu
Route::get('/excel',[AdminController::class,'excel'])->name('shop.excel');
Route::get('/form_import_excel',[AdminController::class,'form_import_excel']);
Route::post('/import_excel',[AdminController::class,'import_excel'])->name('shop.import_excel');

Route::get('/test', function () {
    return view('test');
});
