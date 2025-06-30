<?php

namespace App\Filament\Resources\CartItemResource\Pages;

use App\Filament\Resources\CartItemResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListCartItems extends ListRecords
{
    protected static string $resource = CartItemResource::class;

    protected ?string $heading = 'Chi tiết giỏ hàng';

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
