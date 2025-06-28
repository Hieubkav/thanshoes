<?php

namespace App\Actions;

use Lorisleiva\Actions\Concerns\AsAction;

class ProcessLowStockDataAction
{
    use AsAction;

    public function handle(array $lowStockData): array
    {
        $groupedProducts = []; // Lưu thông tin số lượng theo SKU gốc
        $excludedProducts = []; // Lưu sản phẩm bị loại ra

        // Phân tích dữ liệu từ dòng 6 trở đi (sau header)
        for ($row = 6; isset($lowStockData[$row]); $row++) {
            $sku = $lowStockData[$row]['B'] ?? '';
            if (empty($sku)) continue;

            $needToOrderRaw = $lowStockData[$row]['I'] ?? null;
            $skuParts = explode('-', $sku);
            $baseSku = $skuParts[0];
            $size = $skuParts[1] ?? '';

            // Tính toán số lượng cần nhập
            if (empty($needToOrderRaw) || !is_numeric(str_replace([',', '.'], '', $needToOrderRaw))) {
                $needG = (int)($lowStockData[$row]['G'] ?? 0);
                $needH = (int)($lowStockData[$row]['H'] ?? 0);
                $needToOrder = ($size === '36') ? $needG - $needH : $needG - $needH + 1;
            } else {
                $needToOrderRaw = str_replace(',', '.', $needToOrderRaw);
                $needToOrder = (int)$needToOrderRaw;
            }

            // Nếu không cần nhập thêm, thêm vào danh sách loại trừ
            if ($needToOrder <= 0) {
                $this->addToExcludedProducts($excludedProducts, $baseSku, $size, $needToOrder, $lowStockData[$row], $row, $needToOrderRaw);
                continue;
            }

            // Thêm vào danh sách sản phẩm hợp lệ
            $this->addToValidProducts($groupedProducts, $baseSku, $size, $needToOrder, $lowStockData[$row], $row, $needToOrderRaw);
        }

        // Lọc sản phẩm có tổng số lượng < 6
        $this->filterProductsByMinimumQuantity($groupedProducts, $excludedProducts);

        return [
            'valid' => $groupedProducts,
            'excluded' => $excludedProducts
        ];
    }

    private function addToExcludedProducts(array &$excludedProducts, string $baseSku, string $size, int $needToOrder, array $rowData, int $row, $needToOrderRaw): void
    {
        if (!isset($excludedProducts[$baseSku])) {
            $excludedProducts[$baseSku] = [
                'sizes' => [],
                'reasons' => [],
                'original_data' => [],
            ];
        }
        
        $excludedProducts[$baseSku]['sizes'][$size] = 0;
        $excludedProducts[$baseSku]['reasons'][$size] = "Không cần nhập thêm";
        $excludedProducts[$baseSku]['original_data'][$size] = [
            'need' => (int)($rowData['G'] ?? 0),
            'coming' => (int)($rowData['H'] ?? 0),
            'cần_nhập' => $needToOrder,
            'row' => $row,
            'raw_value' => $needToOrderRaw
        ];
    }

    private function addToValidProducts(array &$groupedProducts, string $baseSku, string $size, int $needToOrder, array $rowData, int $row, $needToOrderRaw): void
    {
        if (!isset($groupedProducts[$baseSku])) {
            $groupedProducts[$baseSku] = [
                'sizes' => [],
                'total_need' => 0,
                'images' => [],
                'version_count' => 0,
                'original_data' => []
            ];
        }

        $groupedProducts[$baseSku]['sizes'][$size] = $needToOrder;
        $groupedProducts[$baseSku]['total_need'] += $needToOrder;
        $groupedProducts[$baseSku]['version_count']++;
        $groupedProducts[$baseSku]['original_data'][$size] = [
            'need' => (int)($rowData['G'] ?? 0),
            'coming' => (int)($rowData['H'] ?? 0),
            'cần_nhập' => $needToOrder,
            'row' => $row,
            'raw_value' => $needToOrderRaw
        ];
    }

    private function filterProductsByMinimumQuantity(array &$groupedProducts, array &$excludedProducts): void
    {
        foreach ($groupedProducts as $baseSku => $productInfo) {
            if ($productInfo['total_need'] < 6) {
                // Chuyển sang danh sách loại trừ
                if (!isset($excludedProducts[$baseSku])) {
                    $excludedProducts[$baseSku] = [
                        'sizes' => $productInfo['sizes'],
                        'reasons' => [],
                        'original_data' => $productInfo['original_data'] ?? [],
                        'total_need' => $productInfo['total_need'],
                        'version_count' => $productInfo['version_count']
                    ];
                } else {
                    $excludedProducts[$baseSku]['sizes'] = array_merge(
                        $excludedProducts[$baseSku]['sizes'] ?? [],
                        $productInfo['sizes']
                    );
                    $excludedProducts[$baseSku]['original_data'] = array_merge(
                        $excludedProducts[$baseSku]['original_data'] ?? [],
                        $productInfo['original_data']
                    );
                    $excludedProducts[$baseSku]['total_need'] = ($excludedProducts[$baseSku]['total_need'] ?? 0) + $productInfo['total_need'];
                    $excludedProducts[$baseSku]['version_count'] = ($excludedProducts[$baseSku]['version_count'] ?? 0) + $productInfo['version_count'];
                }

                // Thêm lý do loại trừ
                foreach ($productInfo['sizes'] as $size => $amount) {
                    $excludedProducts[$baseSku]['reasons'][$size] = "Tổng số lượng cần nhập chỉ có {$productInfo['total_need']} (cần tối thiểu 6)";
                }
                
                unset($groupedProducts[$baseSku]);
            }
        }
    }
}
