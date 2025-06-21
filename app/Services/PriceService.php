<?php

namespace App\Services;

use App\Models\Setting;

class PriceService
{
    public static function calculateDiscountedPrice(float $originalPrice): float
    {
        $setting = Setting::first();
        
        // Nếu không áp dụng giảm giá, trả về giá gốc
        if (!$setting || $setting->apply_price !== 'apply' || $setting->dec_product_price <= 0) {
            return $originalPrice;
        }
        
        // Tính giá sau khi giảm dựa trên loại giảm giá
        $discountedPrice = $originalPrice;
        if ($setting->dec_product_price_type === 'percent') {
            $discountedPrice = $originalPrice * (1 - $setting->dec_product_price / 100);
        } else { // Kiểu 'price'
            $discountedPrice = $originalPrice - $setting->dec_product_price;
            // Đảm bảo giá không âm
            $discountedPrice = max($discountedPrice, 0);
        }
        
        // Áp dụng làm tròn dựa trên cài đặt
        switch ($setting->round_price) {
            case 'up':
                $discountedPrice = ceil($discountedPrice / 1000) * 1000;
                break;
            case 'down':
                $discountedPrice = floor($discountedPrice / 1000) * 1000;
                break;
            case 'balance':
                $discountedPrice = round($discountedPrice / 1000) * 1000;
                break;
        }
        
        return $discountedPrice;
    }
    
    public static function getDiscountInfo(float $originalPrice): array
    {
        $setting = Setting::first();
        $discountedPrice = self::calculateDiscountedPrice($originalPrice);
        $discountAmount = $originalPrice - $discountedPrice;
        $discountPercentage = $originalPrice > 0 ? ($discountAmount / $originalPrice) * 100 : 0;
        
        return [
            'original_price' => $originalPrice,
            'discounted_price' => $discountedPrice,
            'discount_amount' => $discountAmount,
            'discount_percentage' => $discountPercentage,
            'discount_type' => $setting ? $setting->dec_product_price_type : 'percent',
            'is_applied' => $setting && $setting->apply_price === 'apply' && $setting->dec_product_price > 0
        ];
    }
}
