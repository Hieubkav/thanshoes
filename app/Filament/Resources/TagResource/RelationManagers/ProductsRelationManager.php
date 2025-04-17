<?php

namespace App\Filament\Resources\TagResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ProductsRelationManager extends RelationManager
{
    protected static string $relationship = 'products';

    protected static ?string $title = 'Sản phẩm';
    protected static ?string $modelLabel = 'Sản phẩm';
    protected static ?string $pluralModelLabel = 'Danh sách sản phẩm';

    public function form(Form $form): Form
    {
        return $form
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
                Forms\Components\RichEditor::make('description')
                    ->label('Mô tả')
                    ->columnSpanFull(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                Tables\Columns\ImageColumn::make('productImages.image_url')
                    ->label('Ảnh')
                    ->circular()
                    ->stacked()
                    ->limit(2)
                    ->limitedRemainingText()
                    ->size(60),
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
                Tables\Columns\TextColumn::make('brand')
                    ->label('Thương hiệu')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('type')
                    ->label('Loại sản phẩm')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('variants_count')
                    ->label('Số phiên bản')
                    ->counts('variants')
                    ->badge(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\AttachAction::make()
                    ->label('Gán sản phẩm')
                    ->preloadRecordSelect()
                    ->recordSelectSearchColumns(['name', 'brand', 'type'])
            ])
            ->actions([
                Tables\Actions\DetachAction::make()
                    ->label('Gỡ'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DetachBulkAction::make()
                        ->label('Gỡ sản phẩm đã chọn'),
                ]),
            ]);
    }
}