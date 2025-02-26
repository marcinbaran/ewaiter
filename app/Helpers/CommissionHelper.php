<?php

namespace App\Helpers;

class CommissionHelper
{
    public static function calculateCommission(float $totalPriceAfterDiscount, float $provision): float
    {
        return MoneyFormatter::format($totalPriceAfterDiscount * ($provision / 100));
    }
}
