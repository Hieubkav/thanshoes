<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\WebsiteVisit;
use Illuminate\Support\Facades\Log;

class TrackWebsiteVisit
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Chỉ track GET requests và không track admin routes
        if ($request->isMethod('GET') && !$request->is('admin/*') && !$request->is('dashboard/*')) {
            try {
                WebsiteVisit::recordVisit($request);
            } catch (\Exception $e) {
                // Log error nhưng không làm gián đoạn request
                Log::error('Website visit tracking failed: ' . $e->getMessage());
            }
        }

        return $next($request);
    }
}
