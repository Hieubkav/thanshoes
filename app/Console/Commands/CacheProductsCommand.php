<?php

namespace App\Console\Commands;

use App\Services\ProductCacheService;
use Illuminate\Console\Command;

class CacheProductsCommand extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'cache:products {action=warm : Action to perform (warm|clear|refresh)}';

    /**
     * The console command description.
     */
    protected $description = 'Manage product cache (warm up, clear, or refresh)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $action = $this->argument('action');

        switch ($action) {
            case 'warm':
                $this->warmUpCache();
                break;
            case 'clear':
                $this->clearCache();
                break;
            case 'refresh':
                $this->refreshCache();
                break;
            default:
                $this->error("Invalid action. Use 'warm', 'clear', or 'refresh'");
                return 1;
        }

        return 0;
    }

    /**
     * Warm up the cache
     */
    private function warmUpCache()
    {
        $this->info('Warming up product cache...');
        
        $startTime = microtime(true);
        ProductCacheService::warmUpCache();
        $endTime = microtime(true);
        
        $duration = round(($endTime - $startTime) * 1000, 2);
        $this->info("Cache warmed up successfully in {$duration}ms");
    }

    /**
     * Clear the cache
     */
    private function clearCache()
    {
        $this->info('Clearing product cache...');
        
        ProductCacheService::clearCache();
        
        $this->info('Cache cleared successfully');
    }

    /**
     * Refresh the cache (clear then warm up)
     */
    private function refreshCache()
    {
        $this->info('Refreshing product cache...');
        
        $startTime = microtime(true);
        
        // Clear first
        ProductCacheService::clearCache();
        $this->line('Cache cleared...');
        
        // Then warm up
        ProductCacheService::warmUpCache();
        
        $endTime = microtime(true);
        $duration = round(($endTime - $startTime) * 1000, 2);
        
        $this->info("Cache refreshed successfully in {$duration}ms");
    }
}
