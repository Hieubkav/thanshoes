<?php

namespace App\Filament\Resources\ProductResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class VariantsRelationManager extends RelationManager
{
    protected static string $relationship = 'variants';

    protected static ?string $title = 'Phiên bản sản phẩm';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Thông tin phiên bản')
                    ->schema([
                        Forms\Components\TextInput::make('color')
                            ->label('Màu sắc')
                            // ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('size')
                            ->label('Kích thước')
                            // ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('price')
                            ->label('Giá (VNĐ)')
                            ->required()
                            ->integer()
                            ->minValue(0),
                        Forms\Components\TextInput::make('stock')
                            ->label('Số lượng')
                            ->required()
                            ->integer()
                            ->minValue(0),
                    ])
                    ->columns(2)
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('color')
                    ->label('Màu sắc')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('size')
                    ->label('Kích thước')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('price')
                    ->label('Giá')
                    ->money('VND')
                    ->alignment('right')
                    ->sortable(),
                Tables\Columns\TextColumn::make('stock')
                    ->label('Tồn kho')
                    ->alignment('right')
                    ->badge()
                    ->color(fn (int $state): string => 
                        $state > 10 ? 'success' : 
                        ($state > 0 ? 'warning' : 'danger')
                    )
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}