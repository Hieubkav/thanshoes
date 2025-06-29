<?php

namespace App\Observers;

use App\Models\Carousel;
use App\Services\ProductCacheService;

class CarouselCacheObserver
{
    /**
     * Handle the Carousel "created" event.
     */
    public function created(Carousel $carousel): void
    {
        $this->clearCache();
    }

    /**
     * Handle the Carousel "updated" event.
     */
    public function updated(Carousel $carousel): void
    {
        $this->clearCache();
    }

    /**
     * Handle the Carousel "deleted" event.
     */
    public function deleted(Carousel $carousel): void
    {
        $this->clearCache();
    }

    /**
     * Clear carousel cache
     */
    private function clearCache(): void
    {
        ProductCacheService::clearCache();
    }
}
