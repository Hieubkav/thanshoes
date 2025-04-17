<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductResource\Pages;
use App\Filament\Resources\ProductResource\RelationManagers;
use App\Models\Product;
use App\Models\ProductImage;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function getNavigationBadgeColor(): ?string 
    {
        return 'primary';
    }

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationGroup = 'Quản lý bán hàng';
    protected static ?int $navigationSort = 1;
    protected static ?string $modelLabel = 'Sản phẩm';
    protected static ?string $navigationLabel = 'Sản phẩm';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Thông tin sản phẩm')
                    ->description('Nhập thông tin chi tiết sản phẩm')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Tên sản phẩm')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('brand')
                            ->label('Thương hiệu')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('type')
                            ->label('Loại sản phẩm')
                            ->maxLength(255),
                        Forms\Components\Select::make('tags')
                            ->label('Thẻ gán')
                            ->relationship('tags', 'name')
                            ->multiple()
                            ->preload()
                            ->createOptionForm([
                                Forms\Components\TextInput::make('name')
                                    ->label('Tên thẻ')
                                    ->required()
                                    ->maxLength(255),
                            ])
                            ->columnSpanFull(),
                        Forms\Components\RichEditor::make('description')
                            ->label('Mô tả')
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Tên sản phẩm')
                    ->searchable()
                    ->sortable()
                    ->limit(30)
                    ->tooltip(function (Tables\Columns\TextColumn $column): ?string {
                        $state = $column->getState();
                        
                        if (strlen($state) <= 30) {
                            return null;
                        }
                        
                        return $state;
                    })
                    ->wrap(),
                Tables\Columns\ImageColumn::make('productImages.image_url')
                    ->label('Ảnh')
                    ->circular()
                    ->stacked()
                    ->limit(3)
                    ->limitedRemainingText()
                    ->size(60),
                Tables\Columns\TextColumn::make('brand')
                    ->label('Thương hiệu')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('type')
                    ->label('Loại sản phẩm')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('tags.name')
                    ->label('Thẻ gán')
                    ->badge()
                    ->color('primary')
                    ->separator()
                    ->searchable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('variants_count')
                    ->label('Số phiên bản')
                    ->counts('variants')
                    ->badge(),
            ])
            ->defaultSort('created_at', 'desc')
            ->striped()
            ->filters([
                Tables\Filters\SelectFilter::make('brand')
                    ->label('Thương hiệu')
                    ->options(fn () => Product::whereNotNull('brand')
                        ->where('brand', '!=', '')
                        ->distinct()
                        ->pluck('brand', 'brand')
                        ->toArray()
                    )
                    ->searchable(),
                Tables\Filters\SelectFilter::make('type')
                    ->label('Loại sản phẩm') 
                    ->options(fn () => Product::whereNotNull('type')
                        ->where('type', '!=', '')
                        ->distinct()
                        ->pluck('type', 'type')
                        ->toArray()
                    )
                    ->searchable(),
                Tables\Filters\SelectFilter::make('tags')
                    ->label('Thẻ gán')
                    ->relationship('tags', 'name')
                    ->multiple()
                    ->searchable()
                    ->preload(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\ProductImagesRelationManager::class,
            RelationManagers\VariantsRelationManager::class,
            RelationManagers\TagsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'edit' => Pages\EditProduct::route('/{record}/edit'),
        ];
    }
}
