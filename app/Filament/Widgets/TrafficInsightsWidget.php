<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class TrafficInsightsWidget extends Widget
{
    protected static string $view = 'filament.widgets.traffic-insights';
    
    protected int | string | array $columnSpan = 'full';
    
    protected static ?int $sort = 5;

    protected function getHeading(): ?string
    {
        return 'Phân tích chi tiết lưu lượng';
    }

    public function getViewData(): array
    {
        // Top pages được truy cập nhiều nhất (7 ngày)
        $topPages = DB::table('website_visits')
            ->where('visit_date', '>=', Carbon::now()->subDays(7))
            ->select('page_url', DB::raw('COUNT(DISTINCT ip_address) as unique_visitors'), DB::raw('SUM(total_page_views_today) as total_views'))
            ->groupBy('page_url')
            ->orderBy('total_views', 'desc')
            ->limit(10)
            ->get()
            ->map(function ($item) {
                // Làm sạch URL để hiển thị
                $path = parse_url($item->page_url, PHP_URL_PATH);
                $item->clean_url = $path ?: '/';
                return $item;
            });

        // Top referrers (7 ngày)
        $topReferrers = DB::table('website_visits')
            ->where('visit_date', '>=', Carbon::now()->subDays(7))
            ->whereNotNull('referrer')
            ->where('referrer', '!=', '')
            ->select('referrer', DB::raw('COUNT(*) as visits'))
            ->groupBy('referrer')
            ->orderBy('visits', 'desc')
            ->limit(8)
            ->get()
            ->map(function ($item) {
                $host = parse_url($item->referrer, PHP_URL_HOST);
                $item->clean_referrer = $host ?: $item->referrer;
                return $item;
            });

        // Thống kê theo ngày (7 ngày qua)
        $dailyStats = DB::table('website_visits')
            ->where('visit_date', '>=', Carbon::now()->subDays(7))
            ->select('visit_date', DB::raw('COUNT(DISTINCT ip_address) as unique_visitors'), DB::raw('SUM(total_page_views_today) as page_views'))
            ->groupBy('visit_date')
            ->orderBy('visit_date')
            ->get()
            ->keyBy('visit_date');

        // Điền đầy đủ 7 ngày
        $weeklyData = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i)->format('Y-m-d');
            $dayName = Carbon::parse($date)->format('d/m');
            $stats = $dailyStats->get($date);
            $weeklyData[] = [
                'date' => $dayName,
                'visitors' => $stats?->unique_visitors ?? 0,
                'page_views' => $stats?->page_views ?? 0,
            ];
        }

        // Device/Browser insights từ User Agent
        $deviceStats = DB::table('website_visits')
            ->where('visit_date', '>=', Carbon::now()->subDays(7))
            ->whereNotNull('user_agent')
            ->get()
            ->map(function ($visit) {
                $ua = strtolower($visit->user_agent);
                
                // Detect device type
                if (strpos($ua, 'mobile') !== false || strpos($ua, 'android') !== false || strpos($ua, 'iphone') !== false) {
                    $device = 'Mobile';
                } elseif (strpos($ua, 'tablet') !== false || strpos($ua, 'ipad') !== false) {
                    $device = 'Tablet';
                } else {
                    $device = 'Desktop';
                }
                
                // Detect browser
                if (strpos($ua, 'chrome') !== false && strpos($ua, 'edg') === false) {
                    $browser = 'Chrome';
                } elseif (strpos($ua, 'firefox') !== false) {
                    $browser = 'Firefox';
                } elseif (strpos($ua, 'safari') !== false && strpos($ua, 'chrome') === false) {
                    $browser = 'Safari';
                } elseif (strpos($ua, 'edg') !== false) {
                    $browser = 'Edge';
                } else {
                    $browser = 'Other';
                }
                
                return ['device' => $device, 'browser' => $browser];
            })
            ->groupBy('device')
            ->map(function ($group) {
                return $group->count();
            });

        // Peak hours (giờ cao điểm)
        $peakHours = DB::table('website_visits')
            ->where('visit_date', '>=', Carbon::now()->subDays(7))
            ->select(DB::raw('HOUR(updated_at) as hour'), DB::raw('COUNT(*) as visits'))
            ->groupBy(DB::raw('HOUR(updated_at)'))
            ->orderBy('visits', 'desc')
            ->limit(5)
            ->get();

        return [
            'topPages' => $topPages,
            'topReferrers' => $topReferrers,
            'weeklyData' => $weeklyData,
            'deviceStats' => $deviceStats,
            'peakHours' => $peakHours,
        ];
    }
}
