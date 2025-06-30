<?php

namespace App\Filament\Resources\CartResource\Pages;

use App\Filament\Resources\CartResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListCarts extends ListRecords
{
    protected static string $resource = CartResource::class;

    protected ?string $heading = 'Danh sách giỏ hàng';

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
