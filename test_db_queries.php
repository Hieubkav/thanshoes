<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Services\ProductCacheService;
use Illuminate\Support\Facades\DB;

echo "=== Database Query Analysis ===" . PHP_EOL;

// Enable query log
DB::enableQueryLog();

echo "Testing ProductCacheService::getHomepageProducts()..." . PHP_EOL;
$start = microtime(true);
ProductCacheService::getHomepageProducts();
$end = microtime(true);

$queries = DB::getQueryLog();
$queryCount = count($queries);

echo "Execution time: " . round(($end - $start) * 1000, 2) . "ms" . PHP_EOL;
echo "Database queries executed: " . $queryCount . PHP_EOL;

if ($queryCount > 0) {
    echo "Queries detail:" . PHP_EOL;
    foreach ($queries as $i => $query) {
        echo "  " . ($i + 1) . ". " . $query['query'] . PHP_EOL;
        echo "     Bindings: " . json_encode($query['bindings']) . PHP_EOL;
        echo "     Time: " . round($query['time'], 2) . "ms" . PHP_EOL;
        echo PHP_EOL;
    }
} else {
    echo "✓ No database queries (cache working!)" . PHP_EOL;
}

DB::disableQueryLog();
echo PHP_EOL . "=== Cache Status Check ===" . PHP_EOL;

$cache = app('cache');
$homepageCache = $cache->get('homepage_products_v2');
if ($homepageCache) {
    echo "✓ homepage_products_v2 found in cache" . PHP_EOL;
    echo "  Type: " . gettype($homepageCache) . PHP_EOL;
    if (is_object($homepageCache)) {
        echo "  Class: " . get_class($homepageCache) . PHP_EOL;
        if (method_exists($homepageCache, 'count')) {
            echo "  Count: " . $homepageCache->count() . PHP_EOL;
        }
    }
} else {
    echo "✗ homepage_products_v2 not found in cache" . PHP_EOL;
}

echo PHP_EOL . "=== Performance Diagnosis Complete ===" . PHP_EOL;
