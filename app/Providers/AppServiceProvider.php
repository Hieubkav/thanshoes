<?php

namespace App\Providers;

use App\Models\Variant;
use App\Observers\VariantObserver;
use Illuminate\Support\ServiceProvider;
use Livewire\Livewire; // Thêm namespace cho Livewire

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Đăng ký các Livewire components
        Livewire::component('product-image-organizer', \App\Livewire\ProductImageOrganizer::class);
        
        // Đăng ký observer để tự động đồng bộ ảnh
        Variant::observe(VariantObserver::class);
    }
}
