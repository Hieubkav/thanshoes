<?php

namespace App\Filament\Resources\ProductViewResource\Widgets;

use App\Models\ProductView;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class TopProductsWidget extends BaseWidget
{
    protected static ?string $heading = 'Top 10 Sản phẩm được xem nhiều nhất';
    protected static ?string $pollingInterval = '10s';

    protected int | string | array $columnSpan = 'full';

    public function getTableRecordKey($record): string
    {
        return (string) ($record->product_id ?? $record->id ?? uniqid());
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                ProductView::query()
                    ->selectRaw('product_id, SUM(total_views_all_time) as total_views, MAX(id) as id')
                    ->with('product')
                    ->whereHas('product') // Chỉ lấy những record có product tồn tại
                    ->groupBy('product_id')
                    ->orderByDesc('total_views')
                    ->limit(10)
            )
            ->columns([
                Tables\Columns\TextColumn::make('product.name')
                    ->label('Tên sản phẩm')
                    ->searchable()
                    ->limit(40)
                    ->placeholder('Sản phẩm không tồn tại'),
                Tables\Columns\TextColumn::make('product.brand')
                    ->label('Thương hiệu')
                    ->searchable()
                    ->placeholder('N/A'),
                Tables\Columns\TextColumn::make('product.type')
                    ->label('Loại sản phẩm')
                    ->searchable()
                    ->placeholder('N/A'),
                Tables\Columns\TextColumn::make('total_views')
                    ->label('Tổng lượt xem')
                    ->numeric()
                    ->sortable()
                    ->formatStateUsing(fn ($state) => number_format($state ?? 0)),
            ])
            ->paginated(false)
            ->emptyStateHeading('Chưa có dữ liệu')
            ->emptyStateDescription('Chưa có sản phẩm nào được xem.')
            ->emptyStateIcon('heroicon-o-eye-slash');
    }
}
