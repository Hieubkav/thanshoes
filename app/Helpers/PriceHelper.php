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
        $discountAmount = $settings->dec_product_price;
        $discountedPrice = $originalPrice;
        
        if ($settings->dec_product_price_type === 'percent') {
            // Percentage-based discount
            $discountedPrice = $originalPrice * (100 - $discountAmount) / 100;
        } else {
            // Fixed amount discount
            $discountedPrice = $originalPrice - $discountAmount;
            // Make sure price doesn't go below zero
            $discountedPrice = max($discountedPrice, 0);
        }
        
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
        
        return $originalPrice; // Just return the original price as is
    }
    
    /**
     * Get the discount percentage to display
     * 
     * @return int|string The discount percentage or amount (0 if not applied)
     */
    public static function getDiscountPercentage()
    {
        $settings = Setting::first();
        
        if (!$settings || $settings->apply_price !== 'apply') {
            return 0;
        }
        
        if ($settings->dec_product_price_type === 'percent') {
            return $settings->dec_product_price;
        } else {
            // For fixed amount, return the actual amount with currency symbol
            return $settings->dec_product_price;
        }
    }
    
    /**
     * Get the discount display format (% or VND)
     * 
     * @return string The discount format ('percent' or 'price')
     */
    public static function getDiscountType()
    {
        $settings = Setting::first();
        
        if (!$settings || $settings->apply_price !== 'apply') {
            return 'percent';
        }
        
        return $settings->dec_product_price_type;
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
    
    /**
     * Calculate discount amount based on price and settings
     * 
     * @param float $originalPrice The original price
     * @return float The discount amount in currency units
     */
    public static function getDiscountAmount($originalPrice)
    {
        $settings = Setting::first();
        
        if (!$settings || $settings->apply_price !== 'apply') {
            return 0;
        }
        
        if ($settings->dec_product_price_type === 'percent') {
            return $originalPrice * $settings->dec_product_price / 100;
        } else {
            return min($settings->dec_product_price, $originalPrice);
        }
    }
}
