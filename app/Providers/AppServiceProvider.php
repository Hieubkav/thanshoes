<?php

namespace App\Providers;

use App\Models\Carousel;
use App\Models\Variant;
use App\Models\Product;
use App\Models\Setting;
use App\Models\WebsiteDesign;
use App\Observers\CarouselCacheObserver;
use App\Observers\VariantObserver;
use App\Observers\ProductCacheObserver;
use App\Observers\SettingCacheObserver;
use App\Observers\WebsiteDesignCacheObserver;
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
        Livewire::component('live-visitor-counter', \App\Livewire\LiveVisitorCounter::class);
        Livewire::component('realtime-notifications', \App\Livewire\RealtimeNotifications::class);

        // Đăng ký observer để tự động đồng bộ ảnh
        Variant::observe(VariantObserver::class);

        // Đăng ký cache observers để tự động clear cache khi có thay đổi
        Carousel::observe(CarouselCacheObserver::class);
        Product::observe(ProductCacheObserver::class);
        Setting::observe(SettingCacheObserver::class);
        WebsiteDesign::observe(WebsiteDesignCacheObserver::class);
    }
}
