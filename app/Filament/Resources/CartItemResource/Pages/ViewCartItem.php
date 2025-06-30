<?php

namespace App\Filament\Resources\CartItemResource\Pages;

use App\Filament\Resources\CartItemResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists;
use Filament\Infolists\Infolist;

class ViewCartItem extends ViewRecord
{
    protected static string $resource = CartItemResource::class;

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
                        Infolists\Components\TextEntry::make('cart.user.name')
                            ->label('Người dùng')
                            ->placeholder('Khách vãng lai'),
                        Infolists\Components\TextEntry::make('cart.session_id')
                            ->label('Session ID'),
                        Infolists\Components\TextEntry::make('cart.total_amount')
                            ->label('Tổng tiền giỏ hàng')
                            ->money('VND'),
                    ])
                    ->columns(3),
                
                Infolists\Components\Section::make('Thông tin sản phẩm')
                    ->schema([
                        Infolists\Components\TextEntry::make('product.name')
                            ->label('Tên sản phẩm'),
                        Infolists\Components\TextEntry::make('product.brand')
                            ->label('Thương hiệu'),
                        Infolists\Components\TextEntry::make('variant.size')
                            ->label('Size')
                            ->badge(),
                        Infolists\Components\TextEntry::make('variant.color')
                            ->label('Màu')
                            ->badge(),
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
                        Infolists\Components\TextEntry::make('created_at')
                            ->label('Thêm vào giỏ lúc')
                            ->dateTime('d/m/Y H:i'),
                    ])
                    ->columns(2),
            ]);
    }
}
