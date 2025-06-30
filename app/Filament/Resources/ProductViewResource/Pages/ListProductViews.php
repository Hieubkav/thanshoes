<?php

namespace App\Filament\Resources\ProductViewResource\Pages;

use App\Filament\Resources\ProductViewResource;
use App\Models\ProductView;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Components\Tab;
use Illuminate\Database\Eloquent\Builder;
use Carbon\Carbon;

class ListProductViews extends ListRecords
{
    protected static string $resource = ProductViewResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Không có action tạo mới
        ];
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make('Tất cả')
                ->badge(ProductView::count())
                ->badgeColor('primary'),
            'today' => Tab::make('Hôm nay')
                ->badge(ProductView::where('view_date', Carbon::today())->count())
                ->badgeColor('success')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('view_date', Carbon::today())),
            'this_week' => Tab::make('Tuần này')
                ->badge(ProductView::whereBetween('view_date', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])->count())
                ->badgeColor('warning')
                ->modifyQueryUsing(fn (Builder $query) => $query->whereBetween('view_date', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])),
            'this_month' => Tab::make('Tháng này')
                ->badge(ProductView::whereMonth('view_date', Carbon::now()->month)->count())
                ->badgeColor('info')
                ->modifyQueryUsing(fn (Builder $query) => $query->whereMonth('view_date', Carbon::now()->month)),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            \App\Filament\Widgets\LiveTrackingWidget::class,
            ProductViewResource\Widgets\ProductStatsWidget::class,
            ProductViewResource\Widgets\TopProductsWidget::class,
        ];
    }
}
