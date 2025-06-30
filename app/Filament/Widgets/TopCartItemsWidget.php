<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\DB;

class TopCartItemsWidget extends BaseWidget
{
    protected int | string | array $columnSpan = 'full';

    protected static ?int $sort = 2;

    protected function getHeading(): ?string
    {
        return 'Thống kê giỏ hàng';
    }

    protected function getStats(): array
    {
        // Thống kê cơ bản
        $activeCarts = DB::table('carts')
            ->whereExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('cart_items')
                    ->whereRaw('cart_items.cart_id = carts.id');
            })
            ->count();

        $totalValue = DB::table('carts')
            ->whereExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('cart_items')
                    ->whereRaw('cart_items.cart_id = carts.id');
            })
            ->sum('total_amount');

        // Giá trị giỏ hàng trung bình
        $avgCartValue = $activeCarts > 0 ? $totalValue / $activeCarts : 0;

        // Thống kê hôm nay vs hôm qua
        $todayItems = DB::table('cart_items')
            ->whereDate('created_at', today())
            ->sum('quantity');

        $yesterdayItems = DB::table('cart_items')
            ->whereDate('created_at', today()->subDay())
            ->sum('quantity');

        $todayGrowth = $yesterdayItems > 0 ?
            round((($todayItems - $yesterdayItems) / $yesterdayItems) * 100, 1) : 0;

        // Tỷ lệ giỏ hàng bỏ dở (giỏ hàng có sản phẩm nhưng không chuyển thành đơn hàng)
        $cartsWithItems = DB::table('carts')
            ->whereExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('cart_items')
                    ->whereRaw('cart_items.cart_id = carts.id');
            })
            ->where('created_at', '>=', now()->subDays(30))
            ->count();

        $ordersFromCarts = DB::table('orders')
            ->where('created_at', '>=', now()->subDays(30))
            ->count();

        $abandonmentRate = $cartsWithItems > 0 ?
            round((($cartsWithItems - $ordersFromCarts) / $cartsWithItems) * 100, 1) : 0;

        // Size phổ biến nhất
        $popularSize = DB::table('cart_items')
            ->join('variants', 'cart_items.variant_id', '=', 'variants.id')
            ->select('variants.size', DB::raw('COUNT(*) as count'))
            ->groupBy('variants.size')
            ->orderBy('count', 'desc')
            ->first();

        $stats = [
            Stat::make('Giỏ hàng hoạt động', $activeCarts)
                ->description('Giỏ hàng đang chứa sản phẩm')
                ->descriptionIcon('heroicon-m-shopping-cart')
                ->color('primary'),

            Stat::make('Giá trị TB/giỏ', number_format($avgCartValue) . ' ₫')
                ->description('Giá trị trung bình mỗi giỏ hàng')
                ->descriptionIcon('heroicon-m-calculator')
                ->color('success'),

            Stat::make('Thêm hôm nay', $todayItems)
                ->description($todayGrowth >= 0 ? "+{$todayGrowth}% so với hôm qua" : "{$todayGrowth}% so với hôm qua")
                ->descriptionIcon($todayGrowth >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->color($todayGrowth >= 0 ? 'success' : 'danger'),

            Stat::make('Tỷ lệ bỏ dở', $abandonmentRate . '%')
                ->description('Giỏ hàng không chuyển thành đơn (30 ngày)')
                ->descriptionIcon('heroicon-m-exclamation-triangle')
                ->color($abandonmentRate > 70 ? 'danger' : ($abandonmentRate > 50 ? 'warning' : 'success')),

            Stat::make('Size hot nhất', $popularSize ? $popularSize->size : 'N/A')
                ->description($popularSize ? "Được thêm {$popularSize->count} lần" : 'Chưa có dữ liệu')
                ->descriptionIcon('heroicon-m-star')
                ->color('info'),

            Stat::make('Tiềm năng doanh thu', number_format($totalValue) . ' ₫')
                ->description('Tổng giá trị các giỏ hàng hiện tại')
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('warning'),
        ];

        return $stats;
    }
}
