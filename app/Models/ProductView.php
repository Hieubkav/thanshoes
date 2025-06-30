<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class ProductView extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'ip_address',
        'user_agent',
        'referrer',
        'view_date',
        'unique_viewers_today',
        'total_views_today',
        'total_views_all_time',
    ];

    protected $casts = [
        'view_date' => 'date',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Ghi nhận lượt xem sản phẩm
     */
    public static function recordView($productId, $request)
    {
        $ip = $request->ip();
        $userAgent = $request->userAgent();
        $referrer = $request->header('referer');
        $today = Carbon::today();

        // Kiểm tra xem IP này đã xem sản phẩm này hôm nay chưa
        $existingView = self::where('product_id', $productId)
            ->where('ip_address', $ip)
            ->where('view_date', $today)
            ->first();

        if ($existingView) {
            // Cập nhật lượt xem và thời gian truy cập
            $existingView->increment('total_views_today');
            $existingView->increment('total_views_all_time');
            $existingView->touch(); // Cập nhật updated_at
        } else {
            // Tạo record mới cho viewer mới
            self::create([
                'product_id' => $productId,
                'ip_address' => $ip,
                'user_agent' => $userAgent,
                'referrer' => $referrer,
                'view_date' => $today,
                'unique_viewers_today' => 1,
                'total_views_today' => 1,
                'total_views_all_time' => 1,
            ]);
        }
    }

    /**
     * Lấy thống kê sản phẩm hôm nay
     */
    public static function getProductTodayStats($productId)
    {
        $today = Carbon::today();
        
        return [
            'unique_viewers' => self::where('product_id', $productId)
                ->where('view_date', $today)
                ->count(),
            'total_views' => self::where('product_id', $productId)
                ->where('view_date', $today)
                ->sum('total_views_today'),
        ];
    }

    /**
     * Lấy thống kê sản phẩm tổng thể
     */
    public static function getProductAllTimeStats($productId)
    {
        return [
            'total_unique_viewers' => self::where('product_id', $productId)
                ->distinct('ip_address')
                ->count(),
            'total_views' => self::where('product_id', $productId)
                ->sum('total_views_all_time'),
        ];
    }

    /**
     * Lấy top sản phẩm được xem nhiều nhất
     */
    public static function getTopViewedProducts($limit = 10)
    {
        return self::selectRaw('product_id, SUM(total_views_all_time) as total_views')
            ->with('product')
            ->groupBy('product_id')
            ->orderByDesc('total_views')
            ->limit($limit)
            ->get();
    }
}
