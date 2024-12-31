<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductResource\Pages;
use App\Filament\Resources\ProductResource\RelationManagers;
use App\Models\Product;
use Filament\Forms;
use Filament\Forms\Components\Tabs\Tab;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Select::make('hiển thị')
                    ->options([
                        'Hiện' => 'Hiện',
                        'Ẩn' => 'Ẩn',
                    ])
                    ->required(),
                Forms\Components\DatePicker::make('date_of_birth')
                    ->required()
                    ->maxDate(now()),
                Forms\Components\Select::make('variant_id')
                    ->relationship('variants', 'size')
                    ->searchable()
                    ->preload()
                    ->createOptionForm([
                        Forms\Components\TextInput::make('color')
                            ->required(),
                        Forms\Components\TextInput::make('size')
                            ->required(),
                        Forms\Components\TextInput::make('price')
                            ->required(),
                        Forms\Components\TextInput::make('stock')
                            ->required(),
                        Forms\Components\TextInput::make('product_id')
                            ->default('record.id')
                            ->disabled(),
                    ])
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('brand'),
                Tables\Columns\TextColumn::make('type'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('brand')
                    ->options([
                        'Hiện' => 'Hiện',
                        'Ẩn' => 'Ẩn',
                    ]),
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
            //
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
