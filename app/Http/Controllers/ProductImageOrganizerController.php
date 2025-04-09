<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Http\Request;

class ProductImageOrganizerController extends Controller
{
    public function index(Request $request, $productId)
    {
        $product = Product::findOrFail($productId);
        $images = $product->productImages()->orderBy('order')->get();
        
        return view('admin.product-images.organize', [
            'product' => $product,
            'images' => $images
        ]);
    }
    
    public function updateOrder(Request $request, $productId)
    {
        $product = Product::findOrFail($productId);
        $imageIds = $request->input('imageIds', []);
        
        if (!empty($imageIds)) {
            foreach ($imageIds as $index => $imageId) {
                ProductImage::where('id', $imageId)
                    ->where('product_id', $productId)
                    ->update(['order' => $index + 1]);
            }
            
            return response()->json([
                'success' => true,
                'message' => 'Thứ tự ảnh đã được cập nhật'
            ]);
        }
        
        return response()->json([
            'success' => false,
            'message' => 'Không có ảnh nào được cập nhật'
        ], 400);
    }
    
    public function resetOrder(Request $request, $productId)
    {
        $product = Product::findOrFail($productId);
        $images = $product->productImages()->orderBy('id')->get();
        
        foreach ($images as $index => $image) {
            $image->update(['order' => $index + 1]);
        }
        
        return response()->json([
            'success' => true,
            'message' => 'Đã đặt lại thứ tự ảnh',
            'images' => $product->productImages()->orderBy('order')->get()
        ]);
    }
}