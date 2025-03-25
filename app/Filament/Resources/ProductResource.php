<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductResource\Pages;
use App\Filament\Resources\ProductResource\RelationManagers;
use App\Models\Product;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationLabel = 'Sản phẩm';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Group::make()
                ->schema([
                    Forms\Components\Section::make()
                        ->heading('Thông tin cơ bản')
                        ->description('Nhập các thông tin cơ bản của sản phẩm')
                        ->schema([
                            Forms\Components\TextInput::make('name')
                                ->label('Tên sản phẩm')
                                ->required(),
                            Forms\Components\TextInput::make('brand')
                                ->label('Thương hiệu'),
                            Forms\Components\TextInput::make('type')
                                ->label('Loại sản phẩm'),
                        ])->columns(2),
                    
                    Forms\Components\Section::make()
                        ->heading('Mô tả sản phẩm')
                        ->description('Thông tin chi tiết về sản phẩm')
                        ->schema([
                            Forms\Components\RichEditor::make('description')->label('Mô tả'),
                        ])->columnSpan('full'),
                ])->columnSpan('full')
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Tên sản phẩm')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('brand')
                    ->label('Thương hiệu')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('type')
                    ->label('Loại sản phẩm')
                    ->searchable()
                    ->sortable(),
                // Tables\Columns\TextColumn::make('created_at')
                //     ->label('Ngày tạo')
                //     ->dateTime()
                //     ->sortable()
            ])
            ->filters([
                //
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
            RelationManagers\VariantsRelationManager::class,
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
