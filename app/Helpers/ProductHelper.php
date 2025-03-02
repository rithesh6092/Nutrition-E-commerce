<?php

namespace App\Helpers;

class ProductHelper
{
    /**
     * Calculate SVP points from price
     * 
     * @param float $price
     * @return float
     */
    public static function calculateSVPPoints(float $price): float
    {
        return round($price / 100, 2);
    }
} 