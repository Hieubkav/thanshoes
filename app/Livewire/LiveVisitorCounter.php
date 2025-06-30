<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\WebsiteVisit;
use App\Models\ProductView;
use Carbon\Carbon;

class LiveVisitorCounter extends Component
{
    public $todayVisitors = 0;
    public $todayPageViews = 0;
    public $todayProductViews = 0;
    public $onlineVisitors = 0;
    public $lastUpdate;

    public function mount()
    {
        $this->updateStats();
    }

    public function updateStats()
    {
        // Thống kê website hôm nay
        $websiteStats = WebsiteVisit::getTodayStats();
        $this->todayVisitors = $websiteStats['unique_visitors'];
        $this->todayPageViews = $websiteStats['total_page_views'];

        // Thống kê sản phẩm hôm nay
        $productStats = ProductView::where('view_date', Carbon::today())->get();
        $this->todayProductViews = $productStats->sum('total_views_today');

        // Ước tính visitor online (IP duy nhất có hoạt động trong 5 phút gần nhất)
        $this->onlineVisitors = WebsiteVisit::where('updated_at', '>=', Carbon::now()->subMinutes(5))
            ->distinct('ip_address')
            ->count('ip_address');

        $this->lastUpdate = Carbon::now()->format('H:i:s');
    }

    public function render()
    {
        return view('livewire.live-visitor-counter');
    }
}
