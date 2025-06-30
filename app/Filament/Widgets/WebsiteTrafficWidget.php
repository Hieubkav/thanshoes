<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class WebsiteTrafficWidget extends BaseWidget
{
    protected int | string | array $columnSpan = 'full';
    
    protected static ?int $sort = 1;

    protected function getHeading(): ?string
    {
        return 'Thống kê lưu lượng truy cập';
    }

    protected function getStats(): array
    {
        $today = Carbon::today();
        $yesterday = Carbon::yesterday();
        $thisWeek = Carbon::now()->startOfWeek();
        $lastWeek = Carbon::now()->subWeek()->startOfWeek();

        // Thống kê hôm nay vs hôm qua
        $todayVisitors = DB::table('website_visits')
            ->where('visit_date', $today)
            ->count();
            
        $yesterdayVisitors = DB::table('website_visits')
            ->where('visit_date', $yesterday)
            ->count();
            
        $visitorGrowth = $yesterdayVisitors > 0 ? 
            round((($todayVisitors - $yesterdayVisitors) / $yesterdayVisitors) * 100, 1) : 0;

        // Page views hôm nay vs hôm qua
        $todayPageViews = DB::table('website_visits')
            ->where('visit_date', $today)
            ->sum('total_page_views_today');
            
        $yesterdayPageViews = DB::table('website_visits')
            ->where('visit_date', $yesterday)
            ->sum('total_page_views_today');
            
        $pageViewGrowth = $yesterdayPageViews > 0 ? 
            round((($todayPageViews - $yesterdayPageViews) / $yesterdayPageViews) * 100, 1) : 0;

        // Bounce rate (ước tính: visitors chỉ xem 1 trang)
        $singlePageVisitors = DB::table('website_visits')
            ->where('visit_date', $today)
            ->where('total_page_views_today', 1)
            ->count();
            
        $bounceRate = $todayVisitors > 0 ? round(($singlePageVisitors / $todayVisitors) * 100, 1) : 0;

        // Avg pages per session hôm nay
        $avgPagesPerSession = $todayVisitors > 0 ? round($todayPageViews / $todayVisitors, 1) : 0;

        // Top referrer hôm nay
        $topReferrer = DB::table('website_visits')
            ->where('visit_date', $today)
            ->whereNotNull('referrer')
            ->where('referrer', '!=', '')
            ->select('referrer', DB::raw('COUNT(*) as count'))
            ->groupBy('referrer')
            ->orderBy('count', 'desc')
            ->first();

        // Conversion rate (visitors có xem sản phẩm)
        $visitorsViewedProducts = DB::table('website_visits as wv')
            ->join('product_views as pv', function($join) use ($today) {
                $join->on('wv.ip_address', '=', 'pv.ip_address')
                     ->where('pv.view_date', $today);
            })
            ->where('wv.visit_date', $today)
            ->distinct('wv.ip_address')
            ->count();
            
        $productViewRate = $todayVisitors > 0 ? round(($visitorsViewedProducts / $todayVisitors) * 100, 1) : 0;

        return [
            Stat::make('Visitors hôm nay', $todayVisitors)
                ->description($visitorGrowth >= 0 ? "+{$visitorGrowth}% so với hôm qua" : "{$visitorGrowth}% so với hôm qua")
                ->descriptionIcon($visitorGrowth >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->color($visitorGrowth >= 0 ? 'success' : 'danger'),
                
            Stat::make('Page views hôm nay', $todayPageViews)
                ->description($pageViewGrowth >= 0 ? "+{$pageViewGrowth}% so với hôm qua" : "{$pageViewGrowth}% so với hôm qua")
                ->descriptionIcon($pageViewGrowth >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->color($pageViewGrowth >= 0 ? 'success' : 'danger'),
                
            Stat::make('Bounce Rate', $bounceRate . '%')
                ->description('Visitors chỉ xem 1 trang')
                ->descriptionIcon('heroicon-m-arrow-right-start-on-rectangle')
                ->color($bounceRate > 70 ? 'danger' : ($bounceRate > 50 ? 'warning' : 'success')),
                
            Stat::make('Pages/Session', $avgPagesPerSession)
                ->description('Trung bình trang/phiên')
                ->descriptionIcon('heroicon-m-document-duplicate')
                ->color($avgPagesPerSession > 3 ? 'success' : ($avgPagesPerSession > 2 ? 'warning' : 'danger')),
                
            Stat::make('Product View Rate', $productViewRate . '%')
                ->description('Visitors xem sản phẩm')
                ->descriptionIcon('heroicon-m-eye')
                ->color($productViewRate > 30 ? 'success' : ($productViewRate > 15 ? 'warning' : 'danger')),
                
            Stat::make('Top Referrer', $topReferrer ? parse_url($topReferrer->referrer, PHP_URL_HOST) : 'Direct')
                ->description($topReferrer ? "{$topReferrer->count} lượt" : 'Truy cập trực tiếp')
                ->descriptionIcon('heroicon-m-link')
                ->color('info'),
        ];
    }
}
