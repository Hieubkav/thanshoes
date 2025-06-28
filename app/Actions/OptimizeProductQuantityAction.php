<?php

namespace App\Actions;

use Lorisleiva\Actions\Concerns\AsAction;

class OptimizeProductQuantityAction
{
    use AsAction;

    public function handle(array $groupedProducts, array $filteredProductsData, array $lowStockData): array
    {
        foreach ($groupedProducts as $baseSku => &$productInfo) {
            // Thu thập tên sản phẩm
            $productInfo['name'] = $this->getProductName($baseSku, $filteredProductsData);

            // Thu thập hình ảnh
            $productInfo['images'] = $this->collectProductImages($baseSku, $filteredProductsData);

            // Tối ưu số lượng nếu 6 <= total_need < 12
            if ($productInfo['total_need'] >= 6 && $productInfo['total_need'] < 12) {
                $this->optimizeProductQuantity($productInfo, $baseSku, $filteredProductsData, $lowStockData);
            } else {
                // Tính stock_after_order cho sản phẩm không cần tối ưu
                $this->calculateStockAfterOrder($productInfo, $baseSku, $filteredProductsData);
            }
        }

        return $groupedProducts;
    }

    private function getProductName(string $baseSku, array $filteredProductsData): string
    {
        foreach ($filteredProductsData as $row) {
            if (isset($row['N']) && strpos($row['N'], $baseSku) === 0 && !empty($row['A'])) {
                return $row['A'];
            }
        }
        return $baseSku;
    }

    private function collectProductImages(string $baseSku, array $filteredProductsData): array
    {
        $images = [];
        foreach ($filteredProductsData as $row) {
            if (isset($row['N']) && strpos($row['N'], $baseSku) === 0) {
                if (!in_array($row['R'], $images) && !empty($row['R'])) {
                    $images[] = $row['R'];
                }
                foreach (['P', 'Q'] as $col) {
                    if (!in_array($row[$col], $images) && !empty($row[$col])) {
                        $images[] = $row[$col];
                    }
                }
            }
        }
        return $images;
    }

    private function optimizeProductQuantity(array &$productInfo, string $baseSku, array $filteredProductsData, array $lowStockData): void
    {
        $originalSizes = [];
        $originalTotalNeed = $productInfo['total_need'];
        $originalSizesData = $productInfo['sizes'];

        foreach ($productInfo['sizes'] as $size => $quantity) {
            if ($quantity > 0) {
                $originalSizes[] = $size;
            }
        }
        $productInfo['original_sizes'] = $originalSizes;

        // Kiểm tra giày nữ
        $isWomensShoe = $this->isWomensShoe($baseSku, $filteredProductsData);
        $productInfo['is_womens_shoe'] = $isWomensShoe;

        // Xác định ưu tiên size
        $priorities = $isWomensShoe ? ['37', '38'] : ['42', '41', '43'];
        $additionalNeeded = 12 - $productInfo['total_need'];

        // Thu thập thông tin tồn kho
        $targetStocks = $this->collectStockData($priorities, $baseSku, $filteredProductsData, $lowStockData, $productInfo);

        // Kiểm tra khả năng đạt 12 đôi
        $canOptimize = $this->canOptimizeToTwelve($priorities, $targetStocks, $additionalNeeded);

        if (!$canOptimize['possible']) {
            $productInfo['optimization_note'] = $canOptimize['notes'];
        } else {
            // Thực hiện tối ưu
            $this->performOptimization($productInfo, $priorities, $targetStocks, $additionalNeeded, $originalSizesData);
            $this->generateOptimizationNotes($productInfo, $baseSku, $filteredProductsData, $lowStockData, $priorities, $originalSizesData);
        }

        // Tính stock_after_order
        $this->calculateStockAfterOrder($productInfo, $baseSku, $filteredProductsData);
    }

    private function isWomensShoe(string $baseSku, array $filteredProductsData): bool
    {
        $checkSizes = ['41', '42', '43'];
        $foundSizes = [];

        foreach ($filteredProductsData as $row) {
            if (isset($row['N']) && strpos($row['N'], $baseSku) === 0) {
                $skuParts = explode('-', $row['N']);
                $size = $skuParts[1] ?? '';
                if (in_array($size, $checkSizes)) {
                    $minStock = isset($row['AC']) ? intval($row['AC']) : -1;
                    $foundSizes[$size] = $minStock;
                }
            }
        }

        foreach ($checkSizes as $size) {
            if (!isset($foundSizes[$size]) || $foundSizes[$size] !== 0) {
                return false;
            }
        }
        return true;
    }

    private function collectStockData(array $priorities, string $baseSku, array $filteredProductsData, array $lowStockData, array $productInfo): array
    {
        $targetStocks = [];
        $comingData = [];
        $minStocks = [];

        // Khởi tạo
        foreach ($priorities as $size) {
            $targetStocks[$size] = ['stock' => 0, 'current' => 0];
            $comingData[$size] = 0;
            $minStocks[$size] = 0;
        }

        // Lấy dữ liệu từ data_shoes.xlsx
        foreach ($filteredProductsData as $row) {
            if (isset($row['N']) && strpos($row['N'], $baseSku) === 0) {
                $skuParts = explode('-', $row['N']);
                $size = $skuParts[1] ?? '';
                if (in_array($size, $priorities)) {
                    $targetStocks[$size]['stock'] = (int)($row['AA'] ?? 0);
                    $targetStocks[$size]['current'] = $productInfo['sizes'][$size] ?? 0;
                    $minStocks[$size] = (int)($row['AC'] ?? 0);
                }
            }
        }

        // Lấy dữ liệu từ cannhap.xls
        for ($row = 6; isset($lowStockData[$row]); $row++) {
            $sku = $lowStockData[$row]['B'] ?? '';
            if (empty($sku)) continue;
            $skuParts = explode('-', $sku);
            if ($skuParts[0] !== $baseSku) continue;
            $size = $skuParts[1] ?? '';
            if (in_array($size, $priorities)) {
                $comingData[$size] = (int)($lowStockData[$row]['H'] ?? 0);
            }
        }

        return [
            'stocks' => $targetStocks,
            'coming' => $comingData,
            'min_stocks' => $minStocks
        ];
    }

    private function canOptimizeToTwelve(array $priorities, array $stockData, int $additionalNeeded): array
    {
        $totalCanAdd = 0;
        $notes = [];

        foreach ($priorities as $size) {
            $currentStock = $stockData['stocks'][$size]['stock'] ?? 0;
            $currentOrder = $stockData['stocks'][$size]['current'] ?? 0;
            $coming = $stockData['coming'][$size] ?? 0;
            $minStock = $stockData['min_stocks'][$size] ?? 0;
            $futureStock = $currentStock + $coming + $currentOrder;
            $canAdd = max(0, 6 - $futureStock);
            $totalCanAdd += $canAdd;

            $note = "Size $size: Tồn $currentStock, Tồn tối thiểu $minStock, Đang về $coming";
            if ($currentOrder > 0) {
                $note .= ", Nhập $currentOrder";
            }
            $note .= ", Tồn lý thuyết $futureStock -> " . ($canAdd > 0 ? "Có thể nhập thêm $canAdd" : "Không thể nhập thêm");
            $notes[] = $note;
        }

        $possible = $totalCanAdd >= $additionalNeeded;
        if (!$possible) {
            $notes[] = "Tổng có thể nhập thêm $totalCanAdd đôi, không đủ $additionalNeeded đôi để đạt 12";
        }

        return [
            'possible' => $possible,
            'notes' => implode("\n", $notes)
        ];
    }

    // private function performOptimization(array &$productInfo, array $priorities, array $stockData, int $additionalNeeded, array $originalSizesData): void
    // {
    //     $totalAdded = 0;
    //     $currentTotal = $productInfo['total_need'];
    //     $remainingToAdd = 12 - $currentTotal;

    //     foreach ($priorities as $size) {
    //         $currentStock = $stockData['stocks'][$size]['stock'] ?? 0;
    //         $currentOrder = $productInfo['sizes'][$size] ?? 0;
    //         $coming = $stockData['coming'][$size] ?? 0;
    //         $futureStockBefore = $currentStock + $coming + $currentOrder;
    //         $canAdd = max(0, 6 - $futureStockBefore);
    //         $addAmount = min($canAdd, $remainingToAdd - $totalAdded);

    //         if ($addAmount > 0) {
    //             $productInfo['sizes'][$size] = ($productInfo['sizes'][$size] ?? 0) + $addAmount;
    //             $totalAdded += $addAmount;
    //         }

    //         if ($totalAdded >= $remainingToAdd) break;
    //     }

    //     $productInfo['total_need'] = $currentTotal + $totalAdded;
    // }
    private function performOptimization(array &$productInfo, array $priorities, array $stockData, int $additionalNeeded, array $originalSizesData): void
    {
        $totalAdded = 0;
        $currentTotal = $productInfo['total_need'];
        $remainingToAdd = 12 - $currentTotal;

        // Tính toán tồn kho lý thuyết sau nhập cho mỗi size ưu tiên
        $futureStocks = [];
        foreach ($priorities as $size) {
            $currentStock = $stockData['stocks'][$size]['stock'] ?? 0;
            $currentOrder = $productInfo['sizes'][$size] ?? 0;
            $coming = $stockData['coming'][$size] ?? 0;
            $futureStocks[$size] = [
                'current' => $currentStock + $coming + $currentOrder,
                'max_add' => max(0, 6 - ($currentStock + $coming + $currentOrder)),
                'added' => 0
            ];
        }

        // Cân bằng tải - Phân bổ số lượng cần thêm để cân bằng tồn kho
        while ($totalAdded < $remainingToAdd) {
            // Tìm size có tồn kho lý thuyết thấp nhất mà vẫn có thể thêm
            $minStock = PHP_INT_MAX;
            $targetSize = null;

            foreach ($priorities as $size) {
                if ($futureStocks[$size]['max_add'] > 0 && $futureStocks[$size]['current'] < $minStock) {
                    $minStock = $futureStocks[$size]['current'];
                    $targetSize = $size;
                }
            }

            // Nếu không tìm thấy size nào có thể thêm, chuyển sang phương pháp ưu tiên theo thứ tự
            if ($targetSize === null) {
                break;
            }

            // Thêm 1 đôi vào size có tồn kho thấp nhất
            $futureStocks[$targetSize]['current']++;
            $futureStocks[$targetSize]['max_add']--;
            $futureStocks[$targetSize]['added']++;
            $totalAdded++;

            // Nếu đã đạt đủ số lượng cần thêm, dừng vòng lặp
            if ($totalAdded >= $remainingToAdd) {
                break;
            }
        }

        // Nếu vẫn chưa đủ số lượng, sử dụng phương pháp ưu tiên theo thứ tự
        if ($totalAdded < $remainingToAdd) {
            foreach ($priorities as $size) {
                $remainingForSize = min($futureStocks[$size]['max_add'], $remainingToAdd - $totalAdded);
                if ($remainingForSize > 0) {
                    $futureStocks[$size]['added'] += $remainingForSize;
                    $totalAdded += $remainingForSize;
                }

                if ($totalAdded >= $remainingToAdd) {
                    break;
                }
            }
        }

        // Cập nhật số lượng cho mỗi size
        foreach ($priorities as $size) {
            if ($futureStocks[$size]['added'] > 0) {
                $productInfo['sizes'][$size] = ($productInfo['sizes'][$size] ?? 0) + $futureStocks[$size]['added'];
            }
        }

        $productInfo['total_need'] = $currentTotal + $totalAdded;
    }

    private function generateOptimizationNotes(array &$productInfo, string $baseSku, array $filteredProductsData, array $lowStockData, array $priorities, array $originalSizesData): void
    {
        $notes = [];

        // Tạo ghi chú chi tiết cho tất cả các size cần nhập
        $notes[] = "Chi tiết các size cần nhập:";
        foreach (array_keys($productInfo['sizes']) as $size) {
            $currentStock = 0;
            $coming = 0;
            $minStock = 0;
            $orderAmount = $productInfo['sizes'][$size] ?? 0;

            // Lấy dữ liệu từ các file
            foreach ($filteredProductsData as $row) {
                if (isset($row['N']) && $row['N'] === $baseSku . '-' . $size) {
                    $currentStock = (int)($row['AA'] ?? 0);
                    $minStock = (int)($row['AC'] ?? 0);
                    break;
                }
            }

            for ($row = 6; isset($lowStockData[$row]); $row++) {
                $sku = $lowStockData[$row]['B'] ?? '';
                if (empty($sku)) continue;
                $skuParts = explode('-', $sku);
                if ($skuParts[0] !== $baseSku) continue;
                if ($skuParts[1] === $size) {
                    $coming = (int)($lowStockData[$row]['H'] ?? 0);
                    break;
                }
            }

            $futureStockAfter = $currentStock + $coming + $orderAmount;
            $notes[] = "- Size $size: Tồn $currentStock, Tồn tối thiểu $minStock, Đang về $coming, Nhập $orderAmount, Tồn lý thuyết sau nhập $futureStockAfter";
        }

        // Thêm danh sách nhập hàng cuối cùng
        $notes[] = "Nhập hàng: " . implode(", ", array_map(function ($size, $quantity) {
            return "$size: $quantity";
        }, array_keys($productInfo['sizes']), $productInfo['sizes']));

        $productInfo['optimization_note'] = implode("\n", $notes);
    }

    private function calculateStockAfterOrder(array &$productInfo, string $baseSku, array $filteredProductsData): void
    {
        $productInfo['stock_after_order'] = [];
        $allSizes = ['36', '37', '38', '39', '40', '41', '42', '43', '44', '45'];

        foreach ($allSizes as $size) {
            $currentStock = 0;
            $coming = $productInfo['original_data'][$size]['coming'] ?? 0;
            $orderAmount = $productInfo['sizes'][$size] ?? 0;

            foreach ($filteredProductsData as $row) {
                if (isset($row['N']) && $row['N'] === $baseSku . '-' . $size) {
                    $currentStock = (int)($row['AA'] ?? 0);
                    break;
                }
            }

            $productInfo['stock_after_order'][$size] = $currentStock + $coming + $orderAmount;
        }
    }
}
