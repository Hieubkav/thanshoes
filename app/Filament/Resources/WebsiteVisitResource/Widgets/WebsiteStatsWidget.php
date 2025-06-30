<?php

namespace App\Filament\Resources\WebsiteVisitResource\Widgets;

use App\Models\WebsiteVisit;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Carbon\Carbon;

class WebsiteStatsWidget extends BaseWidget
{
    protected static ?string $pollingInterval = '5s';

    protected function getStats(): array
    {
        $todayStats = WebsiteVisit::getTodayStats();
        $allTimeStats = WebsiteVisit::getAllTimeStats();
        
        // Thống kê tuần này
        $weekStats = WebsiteVisit::whereBetween('visit_date', [
            Carbon::now()->startOfWeek(), 
            Carbon::now()->endOfWeek()
        ])->get();
        
        $weekUniqueVisitors = $weekStats->count();
        $weekPageViews = $weekStats->sum('total_page_views_today');
        
        // Thống kê tháng này
        $monthStats = WebsiteVisit::whereMonth('visit_date', Carbon::now()->month)
            ->whereYear('visit_date', Carbon::now()->year)
            ->get();
            
        $monthUniqueVisitors = $monthStats->count();
        $monthPageViews = $monthStats->sum('total_page_views_today');

        return [
            Stat::make('Visitor hôm nay', $todayStats['unique_visitors'])
                ->description('Số người truy cập duy nhất hôm nay')
                ->descriptionIcon('heroicon-m-users')
                ->color('success'),
                
            Stat::make('Lượt xem hôm nay', number_format($todayStats['total_page_views']))
                ->description('Tổng số lượt xem trang hôm nay')
                ->descriptionIcon('heroicon-m-eye')
                ->color('info'),
                
            Stat::make('Visitor tuần này', $weekUniqueVisitors)
                ->description('Số người truy cập duy nhất tuần này')
                ->descriptionIcon('heroicon-m-calendar-days')
                ->color('warning'),
                
            Stat::make('Lượt xem tuần này', number_format($weekPageViews))
                ->description('Tổng số lượt xem trang tuần này')
                ->descriptionIcon('heroicon-m-chart-bar')
                ->color('primary'),
                
            Stat::make('Visitor tháng này', $monthUniqueVisitors)
                ->description('Số người truy cập duy nhất tháng này')
                ->descriptionIcon('heroicon-m-calendar')
                ->color('gray'),

            Stat::make('Lượt xem tháng này', number_format($monthPageViews))
                ->description('Tổng số lượt xem trang tháng này')
                ->descriptionIcon('heroicon-m-chart-pie')
                ->color('indigo'),
                
            Stat::make('Tổng visitor', number_format($allTimeStats['total_unique_visitors']))
                ->description('Tổng số người truy cập từ trước đến nay')
                ->descriptionIcon('heroicon-m-globe-alt')
                ->color('danger'),
        ];
    }
}
