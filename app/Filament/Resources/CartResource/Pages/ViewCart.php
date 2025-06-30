<?php

namespace App\Filament\Resources\CartResource\Pages;

use App\Filament\Resources\CartResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists;
use Filament\Infolists\Infolist;

class ViewCart extends ViewRecord
{
    protected static string $resource = CartResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make('Thông tin giỏ hàng')
                    ->schema([
                        Infolists\Components\TextEntry::make('user.name')
                            ->label('Người dùng')
                            ->placeholder('Khách vãng lai'),
                        Infolists\Components\TextEntry::make('session_id')
                            ->label('Session ID'),
                        Infolists\Components\TextEntry::make('total_amount')
                            ->label('Tổng tiền')
                            ->money('VND'),
                        Infolists\Components\TextEntry::make('original_total_amount')
                            ->label('Tổng tiền gốc')
                            ->money('VND'),
                        Infolists\Components\TextEntry::make('created_at')
                            ->label('Tạo lúc')
                            ->dateTime('d/m/Y H:i'),
                        Infolists\Components\TextEntry::make('updated_at')
                            ->label('Cập nhật lúc')
                            ->dateTime('d/m/Y H:i'),
                    ])
                    ->columns(2),
                
                Infolists\Components\Section::make('Sản phẩm trong giỏ hàng')
                    ->schema([
                        Infolists\Components\RepeatableEntry::make('items')
                            ->label('')
                            ->schema([
                                Infolists\Components\TextEntry::make('product.name')
                                    ->label('Sản phẩm'),
                                Infolists\Components\TextEntry::make('variant.size')
                                    ->label('Size'),
                                Infolists\Components\TextEntry::make('variant.color')
                                    ->label('Màu'),
                                Infolists\Components\TextEntry::make('quantity')
                                    ->label('Số lượng')
                                    ->badge(),
                                Infolists\Components\TextEntry::make('price')
                                    ->label('Giá')
                                    ->money('VND'),
                                Infolists\Components\TextEntry::make('total_price')
                                    ->label('Thành tiền')
                                    ->money('VND')
                                    ->state(fn ($record) => $record->getTotalPrice()),
                            ])
                            ->columns(6),
                    ]),
            ]);
    }
}
