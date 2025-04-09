<?php

namespace App\Filament\Resources\ProductResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use App\Models\VariantImage;
use App\Models\ProductImage;

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
                            ->maxLength(255)
                            ->columnSpan(1),
                        Forms\Components\TextInput::make('size')
                            ->label('Kích thước')
                            ->maxLength(255)
                            ->columnSpan(1),
                        Forms\Components\TextInput::make('price')
                            ->label('Giá (VNĐ)')
                            ->required()
                            ->integer()
                            ->minValue(0)
                            ->columnSpan(1),
                        Forms\Components\TextInput::make('stock')
                            ->label('Số lượng')
                            ->required()
                            ->integer()
                            ->minValue(0)
                            ->columnSpan(1),
                        Forms\Components\TextInput::make('sku')
                            ->label('SKU')
                            ->maxLength(255)
                            ->columnSpan(1),
                    ])
                    ->columns(3),
                
                Forms\Components\Section::make('Hình ảnh phiên bản')
                    ->schema([
                        Forms\Components\FileUpload::make('variantImage.image')
                            ->label('Ảnh phiên bản')
                            ->image()
                            ->imageEditor()
                            ->imageResizeMode('contain')
                            ->imageResizeTargetWidth('400')
                            ->imageResizeTargetHeight('400')
                            ->maxSize(5120)
                            ->disk('public')
                            ->directory('variants')
                            ->panelAspectRatio('1:1')
                            ->panelLayout('compact')
                            ->columnSpan(1)
                            ->afterStateUpdated(function ($state, $set, $record, $livewire) {
                                if ($record && $state) {
                                    // Tạo hoặc cập nhật VariantImage
                                    $variantImage = $record->variantImage;
                                    if (!$variantImage) {
                                        $variantImage = VariantImage::create([
                                            'variant_id' => $record->id,
                                            'image' => $state
                                        ]);
                                    } else {
                                        $variantImage->update(['image' => $state]);
                                    }

                                    // Kiểm tra và tạo/cập nhật ProductImage tương ứng
                                    $product = $record->product;
                                    if ($product) {
                                        $existing_product_image = ProductImage::where('product_id', $product->id)
                                            ->where('variant_image_id', $variantImage->id)
                                            ->first();
                                        
                                        if (!$existing_product_image) {
                                            // Lấy order cao nhất hiện tại
                                            $maxOrder = ProductImage::where('product_id', $product->id)->max('order') ?? 0;
                                            
                                            ProductImage::create([
                                                'product_id' => $product->id,
                                                'image' => $state,
                                                'type' => 'variant',
                                                'variant_image_id' => $variantImage->id,
                                                'order' => $maxOrder + 1,
                                            ]);
                                        } else {
                                            $existing_product_image->update(['image' => $state]);
                                        }

                                        // Refresh form để hiển thị cập nhật
                                        if (method_exists($livewire, 'refreshFormData')) {
                                            $livewire->refreshFormData();
                                        }
                                    }
                                }
                            }),
                        Forms\Components\ViewField::make('preview')
                            ->label('Xem trước')
                            ->view('filament.components.variant-image-preview')
                            ->visible(fn ($record) => $record && $record->variantImage && $record->variantImage->image)
                            ->columnSpanFull(),
                    ])
                    ->columnSpanFull()
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('variantImage.image_url')
                    ->label('Hình ảnh')
                    ->square()
                    ->size(70)
                    ->extraImgAttributes([
                        'class' => 'object-cover',
                        'style' => 'border-radius: 8px; border: 1px solid #e5e7eb;'
                    ])
                    ->defaultImageUrl(fn ($record) => asset('images/default-product.jpg')),
                Tables\Columns\TextColumn::make('color')
                    ->label('Màu sắc')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('size')
                    ->label('Kích thước')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('sku')
                    ->label('SKU')
                    ->searchable(),
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