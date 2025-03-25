<?php

namespace App\Filament\Resources\CustomerResource\RelationManagers;

use App\Filament\Resources\OrderResource;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class OrdersRelationManager extends RelationManager
{
    protected static string $relationship = 'orders';

    protected static ?string $title = 'Đơn hàng';

    protected static ?string $recordTitleAttribute = 'id';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('status')
                    ->options([
                        'pending' => 'Đang xử lý',
                        'processing' => 'Đang chuẩn bị',
                        'shipping' => 'Đang giao hàng',
                        'completed' => 'Đã hoàn thành',
                        'cancelled' => 'Đã hủy'
                    ])
                    ->required()
                    ->label('Trạng thái'),

                Forms\Components\Select::make('payment_method')
                    ->options([
                        'cod' => 'Thanh toán khi nhận hàng',
                        'bank' => 'Chuyển khoản'
                    ])
                    ->required()
                    ->label('Hình thức thanh toán'),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('id')
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('Mã đơn'),
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
                    ->money('VND')
                    ->sortable()
                    ->label('Tổng tiền'),
                Tables\Columns\TextColumn::make('payment_method')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'cod' => 'Thanh toán khi nhận hàng',
                        'bank' => 'Chuyển khoản',
                        default => $state,
                    })
                    ->label('Hình thức thanh toán'),
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
            ])
            ->headerActions([
                // Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('edit_in_order')
                    ->label('Sửa trong OrderResource')
                    ->icon('heroicon-o-pencil-square')
                    ->url(fn ($record) => OrderResource::getUrl('edit', ['record' => $record]))
                    ->openUrlInNewTab(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}