<?php

namespace App\Services;

use App\Models\Carousel;
use App\Models\Product;
use App\Models\Setting;
use App\Models\WebsiteDesign;
use App\Models\Post;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Collection;

class ProductCacheService
{
    const CACHE_TTL = 3600; // 1 hour
    const SHORT_CACHE_TTL = 1800; // 30 minutes

    /**
     * Request-level memoization để tránh deserialize collection lớn nhiều lần
     */
    protected static ?Collection $homepageProducts = null;

    /**
     * Get cached products with eager loading (optimized for homepage)
     */
    public static function getHomepageProducts(): Collection
    {
        if (self::$homepageProducts instanceof Collection) {
            return self::$homepageProducts;
        }

        // For homepage, we need a lighter version to avoid large collection deserialization
        self::$homepageProducts = Cache::remember('homepage_products_v2', self::CACHE_TTL, function () {
            return self::queryWithEagerLoads()
                ->select(['id', 'name', 'slug', 'type', 'brand']) // Remove description for faster load
                ->with(['variants' => function ($query) {
                    $query->select(['id', 'product_id', 'price', 'stock']) // Remove sku for smaller payload
                          ->limit(1); // Limit to only 1 variant per product for homepage
                }])
                ->with(['productImages' => function ($query) {
                    $query->select(['id', 'product_id', 'image'])
                          ->orderBy('order', 'asc')
                          ->limit(1); // Only 1 image per product
                }])
                ->get();
        });

        return self::$homepageProducts;
    }

    /**
     * Base query for products with consistent eager loading
     */
    public static function queryWithEagerLoads(): Builder
    {
        return Product::query()->with(self::eagerLoadRelations());
    }

    /**
     * Define eager loads used across the cache service
     */
    public static function eagerLoadRelations(): array
    {
        return [
            'variants.variantImage',
            'productImages' => function ($query) {
                $query->orderBy('order', 'asc')->limit(1);
            },
            'tags',
        ];
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
     * Get cached carousels
     */
    public static function getCarousels(): Collection
    {
        return Cache::remember('homepage_carousels_v2', self::CACHE_TTL, function () {
            return Carousel::orderBy('created_at', 'desc')->get();
        });
    }

    /**
     * Get products by type with banned names filter (cached)
     */
    public static function getProductsByType(string $typeName): Collection
    {
        // Use a more robust cache key that includes banned names
        $bannedNames = self::getBannedNames();
        $cacheKey = "products_type_{$typeName}_v3_" . md5(serialize($bannedNames));
        
        return Cache::remember($cacheKey, self::SHORT_CACHE_TTL, function () use ($typeName, $bannedNames) {
            // Direct database query instead of filtering from all products
            return self::queryWithEagerLoads()
                ->where('type', $typeName)
                ->get()
                ->filter(function ($product) use ($bannedNames) {
                    // Filter banned names
                    foreach ($bannedNames as $bannedName) {
                        if (!empty($bannedName) && stripos($product->name, $bannedName) !== false) {
                            return false;
                        }
                    }
                    return true;
                })
                ->values();
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
            'banned_product_names_v2',
            'homepage_carousels_v2'
        ];

        foreach ($keys as $key) {
            Cache::forget($key);
        }

        // Clear v3 cache keys (new optimized version)
        $v3Keys = [
            'homepage_products_v2', //美食 v2 is still the main key
            'website_design_v2',
            'has_posts_v2',
            'app_settings_v2',
            'homepage_carousels_v2'
        ];
        
        foreach ($v3Keys as $key) {
            Cache::forget($key);
        }

        // Clear type-specific caches
        self::$homepageProducts = null;
        $types = Cache::get('homepage_types_data_v2', collect())->keys();
        foreach ($types as $type) {
            $bannedNames = self::getBannedNames();
            $cacheKey_v2 = "products_type_{$type}_v2_" . md5(serialize($bannedNames));
            $cacheKey_v3 = "products_type_{$type}_v3_" . md5(serialize($bannedNames));
            Cache::forget($cacheKey_v2);
            Cache::forget($cacheKey_v3);
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

        // Warm up type-specific caches (limit to first 6 types for performance)
        $types = self::getTypesData()->keys()->take(6);
        foreach ($types as $type) {
            self::getProductsByType($type);
        }
    }
}
