<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx\Rels;

class ShopController extends Controller
{
    public function store_front()
    {
        return view('shop.store_front');
    }

    public function cat_filter(Request $request)
    {

        return view('shop.cat_filter');
    }

    public function product_overview($id)
    {
        $product = Product::find($id);
        return view('shop.product_overview', compact('product'));
    }
}
