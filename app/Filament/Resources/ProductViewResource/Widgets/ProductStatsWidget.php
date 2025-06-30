<?php

namespace App\Filament\Resources\ProductViewResource\Widgets;

use App\Models\ProductView;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Carbon\Carbon;

class ProductStatsWidget extends BaseWidget
{
    protected static ?string $pollingInterval = '5s';

    protected function getStats(): array
    {
        // Thống kê hôm nay
        $todayViews = ProductView::where('view_date', Carbon::today())->get();
        $todayUniqueViewers = $todayViews->count();
        $todayTotalViews = $todayViews->sum('total_views_today');
        
        // Thống kê tuần này
        $weekViews = ProductView::whereBetween('view_date', [
            Carbon::now()->startOfWeek(), 
            Carbon::now()->endOfWeek()
        ])->get();
        
        $weekUniqueViewers = $weekViews->count();
        $weekTotalViews = $weekViews->sum('total_views_today');
        
        // Thống kê tháng này
        $monthViews = ProductView::whereMonth('view_date', Carbon::now()->month)
            ->whereYear('view_date', Carbon::now()->year)
            ->get();
            
        $monthUniqueViewers = $monthViews->count();
        $monthTotalViews = $monthViews->sum('total_views_today');

        // Thống kê tổng thể
        $allTimeTotalViews = ProductView::sum('total_views_all_time');

        return [
            Stat::make('Viewer sản phẩm hôm nay', $todayUniqueViewers)
                ->description('Số lượt xem sản phẩm duy nhất hôm nay')
                ->descriptionIcon('heroicon-m-eye')
                ->color('success'),
                
            Stat::make('Lượt xem sản phẩm hôm nay', number_format($todayTotalViews))
                ->description('Tổng số lượt xem sản phẩm hôm nay')
                ->descriptionIcon('heroicon-m-shopping-bag')
                ->color('info'),
                
            Stat::make('Viewer sản phẩm tuần này', $weekUniqueViewers)
                ->description('Số lượt xem sản phẩm duy nhất tuần này')
                ->descriptionIcon('heroicon-m-calendar-days')
                ->color('warning'),
                
            Stat::make('Lượt xem sản phẩm tuần này', number_format($weekTotalViews))
                ->description('Tổng số lượt xem sản phẩm tuần này')
                ->descriptionIcon('heroicon-m-chart-bar')
                ->color('primary'),

            Stat::make('Viewer sản phẩm tháng này', $monthUniqueViewers)
                ->description('Số lượt xem sản phẩm duy nhất tháng này')
                ->descriptionIcon('heroicon-m-calendar')
                ->color('gray'),

            Stat::make('Lượt xem sản phẩm tháng này', number_format($monthTotalViews))
                ->description('Tổng số lượt xem sản phẩm tháng này')
                ->descriptionIcon('heroicon-m-chart-pie')
                ->color('indigo'),
                
            Stat::make('Tổng lượt xem sản phẩm', number_format($allTimeTotalViews))
                ->description('Tổng số lượt xem sản phẩm từ trước đến nay')
                ->descriptionIcon('heroicon-m-globe-alt')
                ->color('danger'),
        ];
    }
}
