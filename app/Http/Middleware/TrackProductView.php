<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\ProductView;
use App\Models\Product;
use Illuminate\Support\Facades\Log;

class TrackProductView
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Chỉ track khi request thành công và là GET method
        if ($request->isMethod('GET') && $response->getStatusCode() === 200) {
            // Lấy product từ route parameter
            $slug = $request->route('slug');
            
            if ($slug) {
                try {
                    $product = Product::where('slug', $slug)->first();
                    
                    if ($product) {
                        ProductView::recordView($product->id, $request);
                    }
                } catch (\Exception $e) {
                    // Log error nhưng không làm gián đoạn request
                    Log::error('Product view tracking failed: ' . $e->getMessage());
                }
            }
        }

        return $response;
    }
}
