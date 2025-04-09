<?php

namespace App\Helpers;

use App\Models\Setting;

class PriceHelper
{
    /**
     * Calculate the discounted price based on settings
     *
     * @param float $originalPrice The original price
     * @return float
     */
    public static function calculateDiscountedPrice($originalPrice)
    {
        $settings = Setting::first();
        
        // If settings don't exist or discount is not applied, return original price
        if (!$settings || $settings->apply_price !== 'apply') {
            return $originalPrice;
        }
        
        // Calculate discounted price
        $discountPercentage = $settings->dec_product_price;
        $discountedPrice = $originalPrice * (100 - $discountPercentage) / 100;
        
        // Round the price based on the setting (to the nearest thousand)
        return self::roundPrice($discountedPrice, $settings->round_price);
    }
    
    /**
     * Get the original price with markup based on the discounted price
     * This is used to show the "original" price that was marked down
     *
     * @param float $originalPrice The original price
     * @return float
     */
    public static function getDisplayOriginalPrice($originalPrice)
    {
        $settings = Setting::first();
        
        // If settings don't exist or discount is not applied, return the same price
        // No markup needed since we're not discounting
        if (!$settings || $settings->apply_price !== 'apply') {
            return $originalPrice;
        }
        
        // If we have settings and discount is applied, calculate the original price
        if ($settings->dec_product_price > 0) {
            // Calculate what the price would be before discount
            return $originalPrice / ((100 - $settings->dec_product_price) / 100);
        }
        
        return $originalPrice;
    }
    
    /**
     * Get the discount percentage to display
     * 
     * @return int The discount percentage (0 if not applied)
     */
    public static function getDiscountPercentage()
    {
        $settings = Setting::first();
        
        if (!$settings || $settings->apply_price !== 'apply') {
            return 0;
        }
        
        return $settings->dec_product_price;
    }
    
    /**
     * Round the price based on setting
     *
     * @param float $price The price to round
     * @param string $roundingMethod The rounding method (up, down, balance)
     * @return float
     */
    private static function roundPrice($price, $roundingMethod)
    {
        // Round to the nearest thousand
        $thousandRemainder = $price % 1000;
        
        if ($roundingMethod === 'up') {
            // Round up to the next thousand
            return $price + (1000 - $thousandRemainder);
        } elseif ($roundingMethod === 'down') {
            // Round down to the previous thousand
            return $price - $thousandRemainder;
        } else { // 'balance' - standard rounding
            // Round to the nearest thousand using standard rounding
            return round($price / 1000) * 1000;
        }
    }
}
