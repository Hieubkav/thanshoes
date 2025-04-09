<?php

namespace App\Filament\Resources;

use App\Forms\Components\CustomSection;
use App\Filament\Resources\OrderResource\Pages;
use App\Models\Order;
use App\Models\Product;
use App\Models\Variant;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'warning';
    }

    protected static ?string $navigationIcon = 'heroicon-o-shopping-bag';
    protected static ?string $navigationGroup = 'Quản lý bán hàng';
    protected static ?string $navigationLabel = 'Đơn hàng';
    protected static ?string $modelLabel = 'Đơn hàng';
    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form->schema([
            CustomSection::make()
                ->heading(__('Thông tin đơn hàng'))
                ->schema([
                    Forms\Components\Select::make('customer_id')
                        ->relationship('customer', 'name')
                        ->required()
                        ->searchable()
                        ->preload()
                        ->label('Khách hàng'),

                    Forms\Components\Group::make()
                        ->relationship('customer')
                        ->schema([
                            Forms\Components\TextInput::make('address')
                                ->label('Địa chỉ')
                                ->disabled(),
                            Forms\Components\TextInput::make('phone')
                                ->label('Số điện thoại')
                                ->disabled(),
                            Forms\Components\TextInput::make('email')
                                ->label('Email')
                                ->disabled(),
                        ])
                        ->columnSpanFull(),

                    Forms\Components\Actions::make([
                        Forms\Components\Actions\Action::make('edit_customer')
                            ->label('Chỉnh sửa thông tin khách hàng')
                            ->url(fn ($record) => CustomerResource::getUrl('edit', ['record' => $record->customer_id]))
                            ->button(),
                    ])->columnSpanFull(),

                    Forms\Components\Select::make('status')
                        ->options([
                            'pending' => 'Đang xử lý',
                            'processing' => 'Đang chuẩn bị',
                            'shipping' => 'Đang giao hàng',
                            'completed' => 'Đã hoàn thành',
                            'cancelled' => 'Đã hủy'
                        ])
                        ->required()
                        ->live()
                        ->afterStateUpdated(function ($state, $old) {
                            if (!request()->route('record')) return;

                            $order = Order::find(request()->route('record'));
                            if (!$order) return;

                            foreach ($order->items as $item) {
                                $variant = Variant::find($item->variant_id);
                                if (!$variant) continue;

                                if ($old === 'completed' && $state !== 'completed') {
                                    $variant->stock += $item->quantity;
                                } elseif ($state === 'completed' && $old !== 'completed') {
                                    $variant->stock -= $item->quantity;
                                }
                                $variant->save();
                            }
                        })
                        ->label('Trạng thái'),

                    Forms\Components\Select::make('payment_method')
                        ->options([
                            'cod' => 'Thanh toán khi nhận hàng',
                            'bank' => 'Chuyển khoản'
                        ])
                        ->default('cod')
                        ->required()
                        ->label('Hình thức thanh toán'),
                ])
                ->columns(3),

            CustomSection::make()
                ->heading(__('Chi tiết sản phẩm'))
                ->schema([
                    Forms\Components\Repeater::make('items')
                        ->relationship('items')
                        ->disabled()
                        ->disableItemCreation()
                        ->disableItemDeletion()
                        ->disableItemMovement()
                        ->schema([
                            Forms\Components\Grid::make([
                                'default' => 3,
                                'sm' => 1,
                                'md' => 2,
                                'lg' => 3
                            ])
                            ->schema([
                                Forms\Components\Group::make([
                                    Forms\Components\ViewField::make('variant_image')
                                        ->view('filament.components.variant-image')
                                        ->label('')
                                        ->visible(fn ($record) => $record && $record->variant && $record->variant->variantImage),
                                ])->columnSpan(1),

                                Forms\Components\Group::make([
                                    Forms\Components\TextInput::make('product_info')
                                        ->label('Thông tin sản phẩm')
                                        ->formatStateUsing(fn ($record) => $record->getProductLabel())
                                        ->disabled(),
                                    Forms\Components\TextInput::make('quantity')
                                        ->label('Số lượng')
                                        ->disabled(),
                                ])->columnSpan(1),

                                Forms\Components\Group::make([
                                    Forms\Components\TextInput::make('price')
                                        ->label('Đơn giá')
                                        ->disabled()
                                        ->formatStateUsing(fn ($state) => number_format($state ?? 0, 0, ',', '.') . 'đ'),
                                ])->columnSpan(1),
                            ])->columnSpanFull(),
                        ]),

                    Forms\Components\Placeholder::make('total_price')
                        ->label('Tổng tiền')
                        ->content(function ($record) {
                            if (!$record?->total_price) return '0đ';

                            return number_format($record->total_price, 0, ',', '.') . 'đ';
                        }),
                ])
                ->collapsible(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('customer.name')
                    ->searchable()
                    ->sortable()
                    ->label('Khách hàng')
                    ->action(function ($record) {
                        return action(
                            \App\Filament\Resources\CustomerResource\Pages\EditCustomer::class,
                            ['record' => $record->customer_id]
                        );
                    }),
                Tables\Columns\TextColumn::make('customer.address')
                    ->searchable()
                    ->sortable()
                    ->toggleable()
                    ->label('Địa chỉ'),
                Tables\Columns\TextColumn::make('customer.phone')
                    ->searchable()
                    ->sortable()
                    ->toggleable()
                    ->label('Số điện thoại'),
                Tables\Columns\TextColumn::make('customer.email')
                    ->searchable()
                    ->sortable()
                    ->toggleable()
                    ->label('Email'),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->colors([
                        'warning' => 'pending',
                        'primary' => 'processing',
                        'info' => 'shipping',
                        'success' => 'completed',
                        'danger' => 'cancelled',
                    ])
                    ->label('Trạng thái'),
                Tables\Columns\TextColumn::make('total_price')
                    ->money('vnd')
                    ->sortable()
                    ->label('Tổng tiền'),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->label('Ngày tạo'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'pending' => 'Đang xử lý',
                        'processing' => 'Đang chuẩn bị',
                        'shipping' => 'Đang giao hàng',
                        'completed' => 'Đã hoàn thành',
                        'cancelled' => 'Đã hủy'
                    ])
                    ->label('Trạng thái'),
                Tables\Filters\Filter::make('created_at')
                    ->form([
                        Forms\Components\DatePicker::make('from')
                            ->label('Từ ngày'),
                        Forms\Components\DatePicker::make('until')
                            ->label('Đến ngày'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['from'],
                                fn (Builder $query, $date): Builder =>
                                    $query->whereDate('created_at', '>=', $date),
                            )
                            ->when(
                                $data['until'],
                                fn (Builder $query, $date): Builder =>
                                    $query->whereDate('created_at', '<=', $date),
                            );
                    })
                    ->label('Ngày tạo'),
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

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListOrders::route('/'),
            'create' => Pages\CreateOrder::route('/create'),
            'edit' => Pages\EditOrder::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with(['items.variant.product', 'items.variant.variantImage', 'customer'])
            ->orderBy('created_at', 'desc');
    }
}
