<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ProductViewInsightsWidget extends Widget
{
    protected static string $view = 'filament.widgets.product-view-insights';
    
    protected int | string | array $columnSpan = 'full';
    
    protected static ?int $sort = 6;

    protected function getHeading(): ?string
    {
        return 'Phân tích lượt xem sản phẩm';
    }

    public function getViewData(): array
    {
        $today = Carbon::today();
        
        // Top sản phẩm được xem nhiều nhất hôm nay
        $topProductsToday = DB::table('product_views')
            ->join('products', 'product_views.product_id', '=', 'products.id')
            ->where('product_views.view_date', $today)
            ->select([
                'products.name',
                'products.brand',
                'products.id as product_id',
                DB::raw('COUNT(DISTINCT product_views.ip_address) as unique_viewers'),
                DB::raw('SUM(product_views.total_views_today) as total_views')
            ])
            ->groupBy(['products.id', 'products.name', 'products.brand'])
            ->orderBy('total_views', 'desc')
            ->limit(10)
            ->get();

        // Top sản phẩm được xem nhiều nhất all time
        $topProductsAllTime = DB::table('product_views')
            ->join('products', 'product_views.product_id', '=', 'products.id')
            ->select([
                'products.name',
                'products.brand',
                'products.id as product_id',
                DB::raw('COUNT(DISTINCT product_views.ip_address) as unique_viewers'),
                DB::raw('SUM(product_views.total_views_all_time) as total_views')
            ])
            ->groupBy(['products.id', 'products.name', 'products.brand'])
            ->orderBy('total_views', 'desc')
            ->limit(10)
            ->get();

        // Conversion rate: từ view sản phẩm sang thêm vào giỏ hàng
        $productsWithViews = DB::table('product_views')
            ->where('view_date', '>=', Carbon::now()->subDays(7))
            ->distinct('product_id')
            ->count();
            
        $productsInCart = DB::table('cart_items')
            ->where('created_at', '>=', Carbon::now()->subDays(7))
            ->distinct('product_id')
            ->count();
            
        $viewToCartRate = $productsWithViews > 0 ? 
            round(($productsInCart / $productsWithViews) * 100, 1) : 0;

        // Thống kê theo thương hiệu
        $brandViews = DB::table('product_views')
            ->join('products', 'product_views.product_id', '=', 'products.id')
            ->where('product_views.view_date', '>=', Carbon::now()->subDays(7))
            ->whereNotNull('products.brand')
            ->where('products.brand', '!=', '')
            ->select([
                'products.brand',
                DB::raw('COUNT(DISTINCT product_views.ip_address) as unique_viewers'),
                DB::raw('SUM(product_views.total_views_today) as total_views')
            ])
            ->groupBy('products.brand')
            ->orderBy('total_views', 'desc')
            ->limit(5)
            ->get();

        // Hoạt động xem sản phẩm theo giờ (hôm nay)
        $hourlyViews = DB::table('product_views')
            ->where('view_date', $today)
            ->select(DB::raw('HOUR(updated_at) as hour'), DB::raw('COUNT(*) as views'))
            ->groupBy(DB::raw('HOUR(updated_at)'))
            ->orderBy('hour')
            ->get()
            ->keyBy('hour');

        // Điền đầy đủ 24 giờ
        $hourlyData = [];
        for ($i = 0; $i < 24; $i++) {
            $hourlyData[$i] = $hourlyViews->get($i)?->views ?? 0;
        }

        // Sản phẩm hot trending (đơn giản hóa: sản phẩm có nhiều view hôm nay)
        $trendingProducts = DB::table('product_views')
            ->join('products', 'product_views.product_id', '=', 'products.id')
            ->where('product_views.view_date', $today)
            ->select([
                'products.name',
                'products.brand',
                'products.id as product_id',
                DB::raw('SUM(product_views.total_views_today) as today_views'),
                DB::raw('COUNT(DISTINCT product_views.ip_address) as unique_viewers')
            ])
            ->groupBy(['products.id', 'products.name', 'products.brand'])
            ->orderBy('today_views', 'desc')
            ->limit(5)
            ->get();

        return [
            'topProductsToday' => $topProductsToday,
            'topProductsAllTime' => $topProductsAllTime,
            'viewToCartRate' => $viewToCartRate,
            'brandViews' => $brandViews,
            'hourlyData' => $hourlyData,
            'trendingProducts' => $trendingProducts,
        ];
    }
}
