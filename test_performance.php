<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Services\ProductCacheService;

echo "=== Performance Test After Optimization ===" . PHP_EOL;
echo "Testing cache performance..." . PHP_EOL;

// Test homepage products
$start = microtime(true);
ProductCacheService::getHomepageProducts();
$end = microtime(true);
echo "Homepage products: " . round(($end - $start) * 1000, 2) . "ms" . PHP_EOL;

// Test products by type
$types = ProductCacheService::getTypesData()->take(3);
foreach($types as $type => $count) {
    $start = microtime(true);
    ProductCacheService::getProductsByType($type);
    $end = microtime(true);
    echo "Products by type '{$type}': " . round(($end - $start) * 1000, 2) . "ms" . PHP_EOL;
}

// Test other cached data
$start = microtime(true);
ProductCacheService::getWebsiteDesign();
$end = microtime(true);
echo "Website design: " . round(($end - $start) * 1000, 2) . "ms" . PHP_EOL;

$start = microtime(true);
ProductCacheService::getCarousels();
$end = microtime(true);
echo "Carousels: " . round(($end - $start) * 1000, 2) . "ms" . PHP_EOL;

$start = microtime(true);
ProductCacheService::hasPosts();
$end = microtime(true);
echo "Has posts check: " . round(($end - $start) * 1000, 2) . "ms" . PHP_EOL;

echo PHP_EOL . "=== Analysis ===" . PHP_EOL;

// Check cache status
echo "Cache keys found:" . PHP_EOL;
$cacheKeys = [
    'homepage_products_v3',
    'website_design_v2',
    'has_posts_v2',
    'homepage_carousels_v2',
    'app_settings_v2'
];

foreach($cacheKeys as $key) {
    $cached = app('cache')->get($key);
    $status = $cached ? "✓ Cached" : "✗ Not cached";
    echo "  {$key}: {$status}" . PHP_EOL;
}

echo PHP_EOL . "Total unique types in cache: " . ProductCacheService::getTypesData()->count() . PHP_EOL;

echo PHP_EOL . "Performance optimization complete!" . PHP_EOL;
