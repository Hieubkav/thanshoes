<?php

namespace App\Console\Commands;

use App\Services\ProductCacheService;
use Illuminate\Console\Command;

class WarmHomepageCache extends Command
{
    protected $signature = 'cache:warm-homepage';
    protected $description = 'Warm up homepage cache for better performance';

    public function handle()
    {
        $this->info('Starting homepage cache warming...');
        
        try {
            $start = microtime(true);
            
            // Warm up all cache items
            ProductCacheService::warmUpCache();
            
            $end = microtime(true);
            $duration = round(($end - $start) * 1000, 2);
            
            $this->info("✅ Homepage cache warmed successfully in {$duration}ms");
            return 0;
            
        } catch (\Exception $e) {
            $this->error("❌ Cache warming failed: " . $e->getMessage());
            return 1;
        }
    }
}
