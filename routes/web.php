<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\ShopController;
use Illuminate\Support\Facades\Route;


Route::get('/', [ShopController::class, 'store_front'])->name('shop.store_front');

Route::get('/excel',[AdminController::class,'excel'])->name('shop.excel');
Route::get('/form_import_excel',[AdminController::class,'form_import_excel']);
Route::post('/import_excel',[AdminController::class,'import_excel'])->name('shop.import_excel');

Route::get('/test',[AdminController::class,'test']);

Route::get('/test2',function(){
    return "xin chào";
});
