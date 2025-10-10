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
        $cart = Cart::getCart(auth('customers')->id(), session()->getId());
        
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

    public function addToCartAjax(Request $request)
    {
        \Log::info('addToCartAjax called', [
            'variant_id' => $request->variant_id,
            'quantity' => $request->quantity,
            'customer_id' => auth('customers')->id(),
            'session_id' => session()->getId()
        ]);

        $request->validate([
            'variant_id' => 'required|exists:variants,id',
            'quantity' => 'required|integer|min:1'
        ]);

        try {
            // Get variant with product
            $variant = \App\Models\Variant::with('product')->findOrFail($request->variant_id);
            
            // Check stock
            if ($variant->stock < $request->quantity) {
                return response()->json([
                    'success' => false,
                    'message' => 'Sản phẩm không đủ số lượng trong kho!'
                ], 400);
            }

            $cart = Cart::getCart(auth('customers')->id(), session()->getId());
            
            // Check if item already exists in cart
            $cartItem = $cart->items()
                ->where('product_id', $variant->product_id)
                ->where('variant_id', $variant->id)
                ->first();
                
            if ($cartItem) {
                $cartItem->increment('quantity');
            } else {
                $cart->items()->create([
                    'product_id' => $variant->product_id,
                    'variant_id' => $variant->id,
                    'quantity' => $request->quantity,
                    'price' => $variant->price
                ]);
            }
            
            $cart->updateTotal();
            
            \Log::info('Product added to cart successfully', [
                'cart_id' => $cart->id,
                'variant_id' => $variant->id,
                'quantity' => $request->quantity,
                'cart_items_count' => $cart->items()->sum('quantity')
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Đã thêm vào giỏ hàng!',
                'cart_count' => $cart->items()->sum('quantity')
            ]);
        } catch (\Exception $e) {
            \Log::error('Error adding to cart', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
            ], 400);
        }
    }
}
