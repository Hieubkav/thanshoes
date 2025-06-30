<?php

namespace App\Filament\Resources\WebsiteVisitResource\Pages;

use App\Filament\Resources\WebsiteVisitResource;
use App\Models\WebsiteVisit;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Components\Tab;
use Illuminate\Database\Eloquent\Builder;
use Carbon\Carbon;

class ListWebsiteVisits extends ListRecords
{
    protected static string $resource = WebsiteVisitResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Không có action tạo mới
        ];
    }

    public function getTabs(): array
    {
        $todayStats = WebsiteVisit::getTodayStats();

        return [
            'all' => Tab::make('Tất cả')
                ->badge(WebsiteVisit::count())
                ->badgeColor('primary'),
            'today' => Tab::make('Hôm nay')
                ->badge($todayStats['unique_visitors'])
                ->badgeColor('success')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('visit_date', Carbon::today())),
            'this_week' => Tab::make('Tuần này')
                ->badge(WebsiteVisit::whereBetween('visit_date', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])->count())
                ->badgeColor('warning')
                ->modifyQueryUsing(fn (Builder $query) => $query->whereBetween('visit_date', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])),
            'this_month' => Tab::make('Tháng này')
                ->badge(WebsiteVisit::whereMonth('visit_date', Carbon::now()->month)->count())
                ->badgeColor('info')
                ->modifyQueryUsing(fn (Builder $query) => $query->whereMonth('visit_date', Carbon::now()->month)),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            \App\Filament\Widgets\LiveTrackingWidget::class,
            WebsiteVisitResource\Widgets\WebsiteStatsWidget::class,
        ];
    }
}
