<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\WebsiteVisit;
use App\Models\ProductView;
use Carbon\Carbon;

class RealtimeNotifications extends Component
{
    public $recentVisits = [];
    public $recentProductViews = [];
    public $showNotifications = true;

    public function mount()
    {
        $this->loadRecentActivity();
    }

    public function loadRecentActivity()
    {
        // Lấy 5 hoạt động gần nhất (trong 10 phút)
        $this->recentVisits = WebsiteVisit::where('updated_at', '>=', Carbon::now()->subMinutes(10))
            ->orderBy('updated_at', 'desc')
            ->limit(5)
            ->get()
            ->map(function ($visit) {
                return [
                    'ip' => $this->maskIp($visit->ip_address),
                    'page' => $this->getPageName($visit->page_url),
                    'time' => $visit->updated_at->diffForHumans(),
                    'referrer' => $visit->referrer ? parse_url($visit->referrer, PHP_URL_HOST) : 'Direct'
                ];
            });

        // Lấy 5 lượt xem sản phẩm gần nhất
        $this->recentProductViews = ProductView::with('product')
            ->where('created_at', '>=', Carbon::now()->subMinutes(10))
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get()
            ->map(function ($view) {
                return [
                    'ip' => $this->maskIp($view->ip_address),
                    'product' => $view->product->name ?? 'Unknown',
                    'time' => $view->created_at->diffForHumans(),
                ];
            });
    }

    private function maskIp($ip)
    {
        // Ẩn một phần IP để bảo mật
        $parts = explode('.', $ip);
        if (count($parts) === 4) {
            return $parts[0] . '.' . $parts[1] . '.***.' . $parts[3];
        }
        return substr($ip, 0, 8) . '***';
    }

    private function getPageName($url)
    {
        $path = parse_url($url, PHP_URL_PATH);
        if ($path === '/' || $path === '') {
            return 'Trang chủ';
        }
        if (strpos($path, '/product/') === 0) {
            return 'Sản phẩm';
        }
        return ucfirst(trim($path, '/'));
    }

    public function toggleNotifications()
    {
        $this->showNotifications = !$this->showNotifications;
    }

    public function render()
    {
        return view('livewire.realtime-notifications');
    }
}
