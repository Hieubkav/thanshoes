<?php

namespace App\Filament\Resources\ProductResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\View\View;
use App\Models\ProductImage;
use Filament\Notifications\Notification;

class ProductImagesRelationManager extends RelationManager
{
    protected static string $relationship = 'productImages';

    protected static ?string $title = 'Hình ảnh sản phẩm';

    protected static ?string $modelLabel = 'Hình ảnh';
    
    protected static ?string $recordTitleAttribute = 'image_url';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('type')
                    ->label('Loại ảnh')
                    ->options([
                        'upload' => 'Upload từ máy tính',
                        'variant' => 'Link từ VariantImage',
                    ])
                    ->default('upload')
                    ->required()
                    ->live()
                    ->afterStateUpdated(function (Forms\Set $set) {
                        $set('image', null);
                        $set('variant_image_id', null);
                        $set('source', null);
                    }),

                Forms\Components\FileUpload::make('image')
                    ->label('Ảnh sản phẩm')
                    ->image()
                    ->imageEditor()
                    ->imageResizeMode('contain')
                    ->imageResizeTargetWidth('400')
                    ->imageResizeTargetHeight('400')
                    ->maxSize(5120)
                    ->disk('public')
                    ->directory('products')
                    ->visibility('public')
                    ->imagePreviewHeight('150')
                    ->hidden(fn (Forms\Get $get) => $get('type') === 'variant')
                    ->required(fn (Forms\Get $get) => $get('type') === 'upload'),

                Forms\Components\Select::make('variant_image_id')
                    ->relationship('variantImage', 'image')
                    ->label('Chọn ảnh từ Variant')
                    ->searchable()
                    ->preload()
                    ->hidden(fn (Forms\Get $get) => $get('type') === 'upload')
                    ->required(fn (Forms\Get $get) => $get('type') === 'variant')
                    ->afterStateUpdated(function ($state, Forms\Set $set) {
                        if ($state) {
                            $set('source', "variant:$state");
                        }
                    }),

                Forms\Components\Hidden::make('order')
                    ->default(fn ($record) => $record?->order ?? 0),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->contentGrid([
                'md' => 2,
                'lg' => 3, 
                'xl' => 5,
            ])
            ->columns([
                Tables\Columns\ImageColumn::make('image_url')
                    ->label('Hình ảnh')
                    ->size(150)
                    ->square()
                    ->extraImgAttributes([
                        'class' => 'object-cover rounded-lg border border-gray-200 shadow-sm transition-all duration-300 hover:scale-105',
                        'style' => 'aspect-ratio: 1;',
                    ]),
                Tables\Columns\TextColumn::make('order')
                    ->label('Thứ tự')
                    ->sortable()
                    ->badge()
                    ->color('primary'),
                Tables\Columns\TextColumn::make('type')
                    ->label('Loại')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'upload' => 'success',
                        'variant' => 'info',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'upload' => 'Upload',
                        'variant' => 'Variant',
                        default => $state,
                    }),
            ])
            ->defaultSort('order')
            ->reorderable('order')
            ->paginated(false)
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label('Thêm ảnh đơn lẻ'),
                Tables\Actions\Action::make('uploadBulkImages')
                    ->label('Tải ảnh hàng loạt')
                    ->icon('heroicon-o-photo')
                    ->color('success')
                    ->form([
                        Forms\Components\FileUpload::make('images')
                            ->label('Ảnh sản phẩm')
                            ->multiple()
                            ->image()
                            ->imageEditor()
                            ->imageResizeMode('contain')
                            ->imageResizeTargetWidth('600')
                            ->imageResizeTargetHeight('600')
                            ->maxSize(5120)
                            ->disk('public')
                            ->directory('products')
                            ->reorderable()
                            ->appendFiles(),
                    ])
                    ->action(function (array $data, $livewire): void {
                        if (empty($data['images']) || !is_array($data['images'])) {
                            return;
                        }

                        $product = $livewire->getOwnerRecord();
                        $maxOrder = $product->productImages()->max('order') ?? 0;

                        foreach ($data['images'] as $index => $image) {
                            $product->productImages()->create([
                                'image' => $image,
                                'type' => 'upload',
                                'order' => $maxOrder + $index + 1,
                            ]);
                        }
                    }),
                Tables\Actions\Action::make('reorganizeImages')
                    ->label('Sắp xếp trực quan')
                    ->icon('heroicon-o-squares-2x2')
                    ->color('warning')
                    ->url(fn ($livewire) => route('product.images.organize', ['product' => $livewire->getOwnerRecord()->id]))
                    ->openUrlInNewTab(),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->modalWidth('md'),
                Tables\Actions\DeleteAction::make(),
                Tables\Actions\Action::make('moveToTop')
                    ->icon('heroicon-m-arrow-up')
                    ->tooltip('Di chuyển lên đầu')
                    ->color('gray')
                    ->action(function ($record, $livewire) {
                        // Lưu thứ tự cũ
                        $oldOrder = $record->order;
                        
                        // Cập nhật tất cả các ảnh có thứ tự < thứ tự hiện tại
                        $record->product->productImages()
                            ->where('order', '<', $oldOrder)
                            ->increment('order');
                        
                        // Di chuyển ảnh này lên đầu (order = 1)
                        $record->update(['order' => 1]);
                    }),
                Tables\Actions\Action::make('moveToBottom')
                    ->icon('heroicon-m-arrow-down')
                    ->tooltip('Di chuyển xuống cuối')
                    ->color('gray')
                    ->action(function ($record, $livewire) {
                        // Lưu thứ tự cũ
                        $oldOrder = $record->order;
                        
                        // Lấy thứ tự lớn nhất
                        $maxOrder = $record->product->productImages()->max('order');
                        
                        // Cập nhật tất cả các ảnh có thứ tự > thứ tự hiện tại
                        $record->product->productImages()
                            ->where('order', '>', $oldOrder)
                            ->decrement('order');
                        
                        // Di chuyển ảnh này xuống cuối
                        $record->update(['order' => $maxOrder]);
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\BulkAction::make('setOrder')
                        ->label('Đặt thứ tự')
                        ->icon('heroicon-o-arrows-up-down')
                        ->form([
                            Forms\Components\TextInput::make('starting_order')
                                ->label('Thứ tự bắt đầu')
                                ->numeric()
                                ->default(1)
                                ->required(),
                        ])
                        ->action(function ($records, array $data) {
                            $startingOrder = (int) $data['starting_order'];
                            
                            foreach ($records as $index => $record) {
                                $record->update(['order' => $startingOrder + $index]);
                            }
                        }),
                ]),
            ]);
    }

    protected function getTableContentFooter(): ?View
    {
        return view('filament.components.product-image-grid-footer', [
            'record' => $this->getOwnerRecord(),
        ]);
    }

    public function getOwnerRecord(): Model
    {
        return parent::getOwnerRecord();
    }

    public function updateImageOrder(array $imageIds): void
    {
        foreach ($imageIds as $index => $imageId) {
            ProductImage::where('id', $imageId)->update(['order' => $index + 1]);
        }
        
        Notification::make()
            ->title('Đã cập nhật thứ tự ảnh')
            ->success()
            ->send();
    }

    public function resetImageOrder(): void
    {
        $product = $this->getOwnerRecord();
        
        $images = ProductImage::where('product_id', $product->id)
            ->orderBy('id')
            ->get();
            
        foreach ($images as $index => $image) {
            $image->update(['order' => $index + 1]);
        }
        
        Notification::make()
            ->title('Đã đặt lại thứ tự ảnh')
            ->success()
            ->send();
    }
}