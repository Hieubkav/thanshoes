<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CartItemResource\Pages;
use App\Models\CartItem;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class CartItemResource extends Resource
{
    protected static ?string $model = CartItem::class;

    protected static ?string $navigationIcon = 'heroicon-o-list-bullet';
    protected static ?string $navigationGroup = 'Bán hàng';
    protected static ?string $navigationLabel = 'Chi tiết giỏ hàng';
    protected static ?string $modelLabel = 'Chi tiết giỏ hàng';
    protected static ?string $pluralModelLabel = 'Chi tiết giỏ hàng';
    protected static ?int $navigationSort = 4;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Thông tin sản phẩm')
                    ->schema([
                        Forms\Components\Select::make('cart_id')
                            ->label('Giỏ hàng')
                            ->relationship('cart', 'id')
                            ->searchable()
                            ->preload()
                            ->required(),
                        Forms\Components\Select::make('product_id')
                            ->label('Sản phẩm')
                            ->relationship('product', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),
                        Forms\Components\Select::make('variant_id')
                            ->label('Phiên bản')
                            ->relationship('variant', 'id')
                            ->searchable()
                            ->preload()
                            ->required(),
                        Forms\Components\TextInput::make('quantity')
                            ->label('Số lượng')
                            ->numeric()
                            ->required()
                            ->minValue(1),
                        Forms\Components\TextInput::make('price')
                            ->label('Giá')
                            ->numeric()
                            ->prefix('₫')
                            ->required(),
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
                Tables\Columns\TextColumn::make('cart.user.name')
                    ->label('Người dùng')
                    ->searchable()
                    ->sortable()
                    ->placeholder('Khách vãng lai'),
                Tables\Columns\TextColumn::make('product.name')
                    ->label('Sản phẩm')
                    ->searchable()
                    ->sortable()
                    ->wrap(),
                Tables\Columns\TextColumn::make('variant.size')
                    ->label('Size')
                    ->sortable()
                    ->badge()
                    ->color('primary'),
                Tables\Columns\TextColumn::make('variant.color')
                    ->label('Màu')
                    ->sortable()
                    ->badge()
                    ->color('secondary'),
                Tables\Columns\TextColumn::make('quantity')
                    ->label('Số lượng')
                    ->sortable()
                    ->badge()
                    ->color('success'),
                Tables\Columns\TextColumn::make('price')
                    ->label('Giá')
                    ->money('VND')
                    ->sortable(),
                Tables\Columns\TextColumn::make('total_price')
                    ->label('Thành tiền')
                    ->money('VND')
                    ->state(fn ($record) => $record->getTotalPrice())
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Thêm lúc')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('product_id')
                    ->label('Sản phẩm')
                    ->relationship('product', 'name')
                    ->searchable()
                    ->preload(),
                Tables\Filters\SelectFilter::make('variant.size')
                    ->label('Size')
                    ->options(function () {
                        return \App\Models\Variant::distinct()
                            ->whereNotNull('size')
                            ->pluck('size', 'size')
                            ->toArray();
                    }),
                Tables\Filters\SelectFilter::make('variant.color')
                    ->label('Màu')
                    ->options(function () {
                        return \App\Models\Variant::distinct()
                            ->whereNotNull('color')
                            ->pluck('color', 'color')
                            ->toArray();
                    }),
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
            ->defaultSort('created_at', 'desc')
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
            'index' => Pages\ListCartItems::route('/'),
            'create' => Pages\CreateCartItem::route('/create'),
            'view' => Pages\ViewCartItem::route('/{record}'),
            'edit' => Pages\EditCartItem::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with(['cart.user', 'product', 'variant']);
    }
}
