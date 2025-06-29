<?php

namespace App\Providers;

use App\Services\ProductCacheService;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;

class ViewServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Share settings cho tất cả views với cache
        View::composer('*', function ($view) {
            $setting = ProductCacheService::getSettings();
            $view->with('setting', $setting);
        });

        // Share filtered products cho component new_arrival với ProductCacheService
        View::composer('component.new_arrival', function ($view) {
            $type_name = $view->getData()['type_name'];

            // Sử dụng ProductCacheService để lấy dữ liệu đã cache
            $products_of_type = ProductCacheService::getProductsByType($type_name);
            $so_luong_types = $products_of_type->count();
            $danh_sach_types = ProductCacheService::getRandomProductsByType($type_name, 4);

            $view->with([
                'products_of_type' => $products_of_type,
                'so_luong_types' => $so_luong_types,
                'danh_sach_types' => $danh_sach_types
            ]);
        });

        // Share carousel data cho component carousel với ProductCacheService
        View::composer('component.carousel', function ($view) {
            $carousels = ProductCacheService::getCarousels();
            $view->with('carousels', $carousels);
        });
    }
}