<?php

namespace App\Observers;

use App\Models\Product;
use App\Models\Setting;
use App\Models\WebsiteDesign;
use App\Services\ProductCacheService;

class ProductCacheObserver
{
    /**
     * Handle the Product "created" event.
     */
    public function created(Product $product): void
    {
        $this->clearCache();
    }

    /**
     * Handle the Product "updated" event.
     */
    public function updated(Product $product): void
    {
        $this->clearCache();
    }

    /**
     * Handle the Product "deleted" event.
     */
    public function deleted(Product $product): void
    {
        $this->clearCache();
    }

    /**
     * Clear product cache
     */
    private function clearCache(): void
    {
        ProductCacheService::clearCache();
    }
}

class SettingCacheObserver
{
    /**
     * Handle the Setting "updated" event.
     */
    public function updated(Setting $setting): void
    {
        ProductCacheService::clearCache();
    }
}

class WebsiteDesignCacheObserver
{
    /**
     * Handle the WebsiteDesign "updated" event.
     */
    public function updated(WebsiteDesign $websiteDesign): void
    {
        ProductCacheService::clearCache();
    }
}
