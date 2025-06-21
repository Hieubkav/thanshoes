<?php

namespace App\Services;

use App\Models\Product;
use App\Models\Setting;
use App\Models\WebsiteDesign;
use App\Models\Post;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Collection;

class ProductCacheService
{
    const CACHE_TTL = 3600; // 1 hour
    const SHORT_CACHE_TTL = 1800; // 30 minutes

    /**
     * Get cached products with eager loading
     */
    public static function getHomepageProducts(): Collection
    {
        return Cache::remember('homepage_products_v2', self::CACHE_TTL, function () {
            return Product::with([
                'variants' => function ($query) {
                    $query->with('variantImage');
                }
            ])->get();
        });
    }

    /**
     * Get cached website design
     */
    public static function getWebsiteDesign()
    {
        return Cache::remember('website_design_v2', self::CACHE_TTL, function () {
            return WebsiteDesign::first();
        });
    }

    /**
     * Check if posts exist (cached)
     */
    public static function hasPosts(): bool
    {
        return Cache::remember('has_posts_v2', self::SHORT_CACHE_TTL, function () {
            return Post::where('status', 1)->exists();
        });
    }

    /**
     * Get cached brands
     */
    public static function getBrands(): Collection
    {
        return Cache::remember('homepage_brands_v2', self::CACHE_TTL, function () {
            return self::getHomepageProducts()
                ->pluck('brand')
                ->filter()
                ->unique()
                ->values();
        });
    }

    /**
     * Get cached types with count data
     */
    public static function getTypesData(): Collection
    {
        return Cache::remember('homepage_types_data_v2', self::CACHE_TTL, function () {
            return self::getHomepageProducts()
                ->pluck('type')
                ->filter()
                ->countBy()
                ->sortDesc();
        });
    }

    /**
     * Get cached settings
     */
    public static function getSettings()
    {
        return Cache::remember('app_settings_v2', self::CACHE_TTL, function () {
            return Setting::first();
        });
    }

    /**
     * Get cached banned product names
     */
    public static function getBannedNames(): array
    {
        return Cache::remember('banned_product_names_v2', self::CACHE_TTL, function () {
            $setting = self::getSettings();
            return array_filter([
                $setting->ban_name_product_one,
                $setting->ban_name_product_two,
                $setting->ban_name_product_three,
                $setting->ban_name_product_four,
                $setting->ban_name_product_five
            ]);
        });
    }

    /**
     * Get products by type with banned names filter (cached)
     */
    public static function getProductsByType(string $typeName): Collection
    {
        $bannedNames = self::getBannedNames();
        $cacheKey = "products_type_{$typeName}_v2_" . md5(serialize($bannedNames));
        
        return Cache::remember($cacheKey, self::SHORT_CACHE_TTL, function () use ($typeName, $bannedNames) {
            $products = self::getHomepageProducts()->where('type', $typeName);
            
            // Filter banned names
            foreach ($bannedNames as $bannedName) {
                if (!empty($bannedName)) {
                    $products = $products->filter(function ($product) use ($bannedName) {
                        return stripos($product->name, $bannedName) === false;
                    });
                }
            }
            
            return $products->values();
        });
    }

    /**
     * Get random products by type for display
     */
    public static function getRandomProductsByType(string $typeName, int $limit = 4): Collection
    {
        $products = self::getProductsByType($typeName);
        
        return $products->count() > $limit 
            ? $products->random(min($limit, $products->count()))
            : $products;
    }

    /**
     * Clear all product-related cache
     */
    public static function clearCache(): void
    {
        $keys = [
            'homepage_products_v2',
            'website_design_v2',
            'has_posts_v2',
            'homepage_brands_v2',
            'homepage_types_data_v2',
            'app_settings_v2',
            'banned_product_names_v2'
        ];

        foreach ($keys as $key) {
            Cache::forget($key);
        }

        // Clear type-specific caches
        $types = Cache::get('homepage_types_data_v2', collect())->keys();
        foreach ($types as $type) {
            $bannedNames = self::getBannedNames();
            $cacheKey = "products_type_{$type}_v2_" . md5(serialize($bannedNames));
            Cache::forget($cacheKey);
        }
    }

    /**
     * Warm up cache (useful for scheduled tasks)
     */
    public static function warmUpCache(): void
    {
        self::getHomepageProducts();
        self::getWebsiteDesign();
        self::hasPosts();
        self::getBrands();
        self::getTypesData();
        self::getSettings();
        self::getBannedNames();

        // Warm up type-specific caches
        $types = self::getTypesData()->keys();
        foreach ($types as $type) {
            self::getProductsByType($type);
        }
    }
}
