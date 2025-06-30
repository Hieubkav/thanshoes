<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductViewResource\Pages;
use App\Models\ProductView;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Carbon\Carbon;

class ProductViewResource extends Resource
{
    protected static ?string $model = ProductView::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-bag';
    protected static ?string $navigationGroup = 'Thống kê';
    protected static ?string $navigationLabel = 'Lượt xem Sản phẩm';
    protected static ?string $modelLabel = 'Lượt xem Sản phẩm';
    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('product_id')
                    ->label('Sản phẩm')
                    ->relationship('product', 'name')
                    ->disabled(),
                Forms\Components\TextInput::make('ip_address')
                    ->label('Địa chỉ IP')
                    ->disabled(),
                Forms\Components\Textarea::make('user_agent')
                    ->label('User Agent')
                    ->disabled(),
                Forms\Components\TextInput::make('referrer')
                    ->label('Trang giới thiệu')
                    ->disabled(),
                Forms\Components\DatePicker::make('view_date')
                    ->label('Ngày xem')
                    ->disabled(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('product.name')
                    ->label('Tên sản phẩm')
                    ->searchable()
                    ->sortable()
                    ->limit(30),
                Tables\Columns\TextColumn::make('product.brand')
                    ->label('Thương hiệu')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('ip_address')
                    ->label('Địa chỉ IP')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('view_date')
                    ->label('Ngày xem')
                    ->date('d/m/Y')
                    ->sortable(),
                Tables\Columns\TextColumn::make('total_views_today')
                    ->label('Lượt xem hôm nay')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('total_views_all_time')
                    ->label('Tổng lượt xem')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Thời gian tạo')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\Filter::make('today')
                    ->label('Hôm nay')
                    ->query(fn (Builder $query): Builder => $query->where('view_date', Carbon::today())),
                Tables\Filters\Filter::make('this_week')
                    ->label('Tuần này')
                    ->query(fn (Builder $query): Builder => $query->whereBetween('view_date', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])),
                Tables\Filters\Filter::make('this_month')
                    ->label('Tháng này')
                    ->query(fn (Builder $query): Builder => $query->whereMonth('view_date', Carbon::now()->month)),
                Tables\Filters\SelectFilter::make('product')
                    ->label('Sản phẩm')
                    ->relationship('product', 'name')
                    ->searchable()
                    ->preload(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
            ])
            ->defaultSort('created_at', 'desc')
            ->paginated([10, 25, 50, 100]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProductViews::route('/'),
            'view' => Pages\ViewProductView::route('/{record}'),
        ];
    }

    public static function canCreate(): bool
    {
        return false; // Không cho phép tạo mới thủ công
    }

    public static function canEdit($record): bool
    {
        return false; // Không cho phép chỉnh sửa
    }

    public static function canDelete($record): bool
    {
        return false; // Không cho phép xóa
    }
}
