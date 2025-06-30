<?php

namespace App\Filament\Resources;

use App\Filament\Resources\WebsiteVisitResource\Pages;
use App\Models\WebsiteVisit;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Carbon\Carbon;

class WebsiteVisitResource extends Resource
{
    protected static ?string $model = WebsiteVisit::class;

    protected static ?string $navigationIcon = 'heroicon-o-chart-bar';
    protected static ?string $navigationGroup = 'Thống kê';
    protected static ?string $navigationLabel = 'Lượt truy cập Website';
    protected static ?string $modelLabel = 'Lượt truy cập Website';
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('ip_address')
                    ->label('Địa chỉ IP')
                    ->disabled(),
                Forms\Components\Textarea::make('user_agent')
                    ->label('User Agent')
                    ->disabled(),
                Forms\Components\TextInput::make('page_url')
                    ->label('URL trang')
                    ->disabled(),
                Forms\Components\TextInput::make('referrer')
                    ->label('Trang giới thiệu')
                    ->disabled(),
                Forms\Components\DatePicker::make('visit_date')
                    ->label('Ngày truy cập')
                    ->disabled(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('ip_address')
                    ->label('Địa chỉ IP')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('page_url')
                    ->label('URL trang')
                    ->searchable()
                    ->limit(50),
                Tables\Columns\TextColumn::make('referrer')
                    ->label('Trang giới thiệu')
                    ->limit(30)
                    ->placeholder('Trực tiếp'),
                Tables\Columns\TextColumn::make('visit_date')
                    ->label('Ngày truy cập')
                    ->date('d/m/Y')
                    ->sortable(),
                Tables\Columns\TextColumn::make('total_page_views_today')
                    ->label('Lượt xem hôm nay')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('total_page_views_all_time')
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
                    ->query(fn (Builder $query): Builder => $query->where('visit_date', Carbon::today())),
                Tables\Filters\Filter::make('this_week')
                    ->label('Tuần này')
                    ->query(fn (Builder $query): Builder => $query->whereBetween('visit_date', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])),
                Tables\Filters\Filter::make('this_month')
                    ->label('Tháng này')
                    ->query(fn (Builder $query): Builder => $query->whereMonth('visit_date', Carbon::now()->month)),
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
            'index' => Pages\ListWebsiteVisits::route('/'),
            'view' => Pages\ViewWebsiteVisit::route('/{record}'),
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
