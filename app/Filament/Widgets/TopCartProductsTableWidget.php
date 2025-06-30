<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;
use Illuminate\Support\Facades\DB;

class TopCartProductsTableWidget extends Widget
{
    protected static string $view = 'filament.widgets.top-cart-products-table';
    
    protected int | string | array $columnSpan = 'full';
    
    protected static ?int $sort = 3;

    public function getViewData(): array
    {
        // Lấy top 20 sản phẩm được thêm vào giỏ hàng nhiều nhất
        $topProducts = DB::table('cart_items')
            ->join('products', 'cart_items.product_id', '=', 'products.id')
            ->join('variants', 'cart_items.variant_id', '=', 'variants.id')
            ->select([
                'products.name as product_name',
                'products.brand',
                'variants.size',
                'variants.color',
                'cart_items.product_id',
                DB::raw('COUNT(*) as total_added'),
                DB::raw('SUM(cart_items.quantity) as total_quantity'),
                DB::raw('MAX(cart_items.created_at) as last_added')
            ])
            ->groupBy([
                'cart_items.product_id', 
                'cart_items.variant_id', 
                'products.name', 
                'products.brand',
                'variants.size', 
                'variants.color'
            ])
            ->orderBy('total_added', 'desc')
            ->limit(20)
            ->get();

        return [
            'topProducts' => $topProducts
        ];
    }
}
