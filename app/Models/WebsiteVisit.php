<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class WebsiteVisit extends Model
{
    use HasFactory;

    protected $fillable = [
        'ip_address',
        'user_agent',
        'page_url',
        'referrer',
        'visit_date',
        'unique_visitors_today',
        'total_page_views_today',
        'total_page_views_all_time',
    ];

    protected $casts = [
        'visit_date' => 'date',
    ];

    /**
     * Ghi nhận lượt truy cập website
     */
    public static function recordVisit($request)
    {
        $ip = $request->ip();
        $userAgent = $request->userAgent();
        $pageUrl = $request->fullUrl();
        $referrer = $request->header('referer');
        $today = Carbon::today();

        // Kiểm tra xem IP này đã truy cập hôm nay chưa
        $existingVisit = self::where('ip_address', $ip)
            ->where('visit_date', $today)
            ->first();

        if ($existingVisit) {
            // Cập nhật lượt xem trang và thời gian truy cập
            $existingVisit->increment('total_page_views_today');
            $existingVisit->increment('total_page_views_all_time');
            $existingVisit->touch(); // Cập nhật updated_at
        } else {
            // Tạo record mới cho visitor mới
            self::create([
                'ip_address' => $ip,
                'user_agent' => $userAgent,
                'page_url' => $pageUrl,
                'referrer' => $referrer,
                'visit_date' => $today,
                'unique_visitors_today' => 1,
                'total_page_views_today' => 1,
                'total_page_views_all_time' => 1,
            ]);
        }
    }

    /**
     * Lấy thống kê hôm nay
     */
    public static function getTodayStats()
    {
        $today = Carbon::today();
        
        return [
            'unique_visitors' => self::where('visit_date', $today)->count(),
            'total_page_views' => self::where('visit_date', $today)->sum('total_page_views_today'),
        ];
    }

    /**
     * Lấy thống kê tổng thể
     */
    public static function getAllTimeStats()
    {
        return [
            'total_unique_visitors' => self::distinct('ip_address')->count(),
            'total_page_views' => self::sum('total_page_views_all_time'),
        ];
    }
}
