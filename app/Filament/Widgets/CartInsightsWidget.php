<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;
use Illuminate\Support\Facades\DB;

class CartInsightsWidget extends Widget
{
    protected static string $view = 'filament.widgets.cart-insights';
    
    protected int | string | array $columnSpan = 'full';
    
    protected static ?int $sort = 4;

    protected function getHeading(): ?string
    {
        return 'Phân tích chi tiết giỏ hàng';
    }

    public function getViewData(): array
    {
        // Top thương hiệu được thêm vào giỏ nhiều nhất
        $topBrands = DB::table('cart_items')
            ->join('products', 'cart_items.product_id', '=', 'products.id')
            ->select('products.brand', DB::raw('COUNT(*) as total_added'), DB::raw('SUM(cart_items.quantity) as total_quantity'))
            ->whereNotNull('products.brand')
            ->where('products.brand', '!=', '')
            ->groupBy('products.brand')
            ->orderBy('total_added', 'desc')
            ->limit(5)
            ->get();

        // Top size được yêu thích
        $topSizes = DB::table('cart_items')
            ->join('variants', 'cart_items.variant_id', '=', 'variants.id')
            ->select('variants.size', DB::raw('COUNT(*) as total_added'), DB::raw('SUM(cart_items.quantity) as total_quantity'))
            ->groupBy('variants.size')
            ->orderBy('total_added', 'desc')
            ->limit(10)
            ->get();

        // Giỏ hàng có giá trị cao (trên 1 triệu)
        $highValueCarts = DB::table('carts')
            ->where('total_amount', '>', 1000000)
            ->whereExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('cart_items')
                    ->whereRaw('cart_items.cart_id = carts.id');
            })
            ->count();

        return [
            'topBrands' => $topBrands,
            'topSizes' => $topSizes,
            'highValueCarts' => $highValueCarts,
        ];
    }
}
