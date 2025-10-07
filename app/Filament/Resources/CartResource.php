<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CartResource\Pages;
use App\Models\Cart;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class CartResource extends Resource
{
    protected static ?string $model = Cart::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-cart';
    protected static ?string $navigationGroup = 'Bán hàng';
    protected static ?string $navigationLabel = 'Giỏ hàng';
    protected static ?string $modelLabel = 'Giỏ hàng';
    protected static ?string $pluralModelLabel = 'Giỏ hàng';
    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Thông tin giỏ hàng')
                    ->schema([
                        Forms\Components\Select::make('user_id')
                            ->label('Người dùng (Admin)')
                            ->relationship('user', 'name')
                            ->searchable()
                            ->preload(),
                        Forms\Components\Select::make('customer_id')
                            ->label('Khách hàng')
                            ->relationship('customer', 'name')
                            ->searchable()
                            ->preload(),
                        Forms\Components\TextInput::make('session_id')
                            ->label('Session ID')
                            ->disabled(),
                        Forms\Components\TextInput::make('total_amount')
                            ->label('Tổng tiền')
                            ->numeric()
                            ->prefix('₫')
                            ->disabled(),
                        Forms\Components\TextInput::make('original_total_amount')
                            ->label('Tổng tiền gốc')
                            ->numeric()
                            ->prefix('₫')
                            ->disabled(),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('ID')
                    ->sortable(),
                Tables\Columns\TextColumn::make('customer.name')
                    ->label('Khách hàng')
                    ->searchable()
                    ->sortable()
                    ->placeholder('Khách vãng lai'),
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Người dùng (Admin)')
                    ->searchable()
                    ->sortable()
                    ->placeholder('---')
                    ->toggleable(),
                Tables\Columns\TextColumn::make('session_id')
                    ->label('Session ID')
                    ->searchable()
                    ->limit(20)
                    ->tooltip(function ($record) {
                        return $record->session_id;
                    })
                    ->toggleable(),
                Tables\Columns\TextColumn::make('items_count')
                    ->label('Số sản phẩm')
                    ->counts('items')
                    ->badge()
                    ->color('primary'),
                Tables\Columns\TextColumn::make('total_amount')
                    ->label('Tổng tiền')
                    ->money('VND')
                    ->sortable(),
                Tables\Columns\TextColumn::make('original_total_amount')
                    ->label('Tổng tiền gốc')
                    ->money('VND')
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Tạo lúc')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Cập nhật lúc')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(),
            ])
            ->filters([
                Tables\Filters\Filter::make('has_customer')
                    ->label('Khách đã đăng nhập')
                    ->query(fn (Builder $query): Builder => $query->whereNotNull('customer_id')),
                Tables\Filters\Filter::make('guest')
                    ->label('Khách vãng lai')
                    ->query(fn (Builder $query): Builder => $query->whereNull('customer_id')),
                Tables\Filters\Filter::make('has_items')
                    ->label('Có sản phẩm')
                    ->query(fn (Builder $query): Builder => $query->has('items')),
                Tables\Filters\Filter::make('empty')
                    ->label('Giỏ hàng trống')
                    ->query(fn (Builder $query): Builder => $query->doesntHave('items')),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
                    ->requiresConfirmation(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->requiresConfirmation(),
                ]),
            ])
            ->defaultSort('updated_at', 'desc')
            ->striped();
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCarts::route('/'),
            'create' => Pages\CreateCart::route('/create'),
            'view' => Pages\ViewCart::route('/{record}'),
            'edit' => Pages\EditCart::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with(['user', 'customer', 'items'])
            ->withCount('items');
    }
}
