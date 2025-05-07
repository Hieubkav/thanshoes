<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\Post;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;

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

    public function product_overview($slug)
    {
        $product = Product::where('slug', $slug)->firstOrFail();
        return view('shop.product_overview', compact('product'));
    }

    public function checkout()
    {
        $cart = Cart::getCart(auth()->id(), session()->getId());
        
        if (!$cart || $cart->items()->count() === 0) {
            return redirect()->route('shop.store_front')
                ->with('error', 'Giỏ hàng của bạn đang trống.');
        }
        
        return view('shop.checkout-page');
    }

    public function posts_list()
    {
        $posts = Post::where('status', 'show')
            ->latest()
            ->paginate(9);
        return view('shop.post_list', compact('posts'));
    }

    public function post_detail($id)
    {
        $post = Post::where('status', 'show')
            ->findOrFail($id);
        return view('shop.post_detail', compact('post'));
    }
}
