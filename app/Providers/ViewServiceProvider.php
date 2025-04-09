<?php

namespace App\Providers;

use App\Models\Product;
use App\Models\Setting;
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
        // Share settings cho tất cả views
        View::composer('*', function ($view) {
            $setting = Setting::first();
            $view->with('setting', $setting);
        });

        // Share filtered products cho component new_arrival
        View::composer('component.new_arrival', function ($view) {
            $setting = Setting::first();
            $type_name = $view->getData()['type_name'];

            $query = Product::where('type', $type_name);
            
            // Lọc bỏ các sản phẩm bị cấm
            $bannedNames = array_filter([
                $setting->ban_name_product_one,
                $setting->ban_name_product_two,
                $setting->ban_name_product_three,
                $setting->ban_name_product_four,
                $setting->ban_name_product_five
            ]);
            
            foreach($bannedNames as $bannedName) {
                if(!empty($bannedName)) {
                    $query->where('name', 'not like', '%' . $bannedName . '%');
                }
            }

            // Lấy tất cả sản phẩm sau khi đã lọc
            $products_of_type = $query->get();
            $so_luong_types = $products_of_type->count();
            
            // Lấy ngẫu nhiên 4 sản phẩm từ danh sách đã lọc
            $danh_sach_types = $products_of_type->shuffle()->take(4);

            $view->with([
                'products_of_type' => $products_of_type,
                'so_luong_types' => $so_luong_types,
                'danh_sach_types' => $danh_sach_types
            ]);
        });
    }
}