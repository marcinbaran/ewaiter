<?php

namespace App\Helpers;

use App\Exceptions\ApiExceptions\General\BadConfigurationException;

final class PointsHelper
{
    private const string POINTS_RATIO_CONFIG_PATH = 'admanager.ratio';

    private const string MIN_AMOUNT_TO_BE_PAID_PATH = 'admanager.minimum_amount_to_be_paid';

    public static function calculatePointsValue(?float $points): float
    {
        if ($points === null) {
            return 0;
        }

        return round($points / self::getPointsRatio(), 2);
    }

    public static function calculateMaxPointsAllowed(float $totalFoodPrice): int
    {
        return ($totalFoodPrice - self::getMinimumAmountToBePaid()) * self::getPointsRatio();
    }

    public static function getPointsRatio(): int
    {
        $pointsRatio = config(self::POINTS_RATIO_CONFIG_PATH);

        if ($pointsRatio === null || $pointsRatio <= 0) {
            throw new BadConfigurationException([BadConfigurationException::MISSING_CONFIG_ENTRY_KEY => 'points ratio']);
        }

        return $pointsRatio;
    }

    public static function getMinimumAmountToBePaid(): float
    {
        $minAmountToBePaid = config(self::MIN_AMOUNT_TO_BE_PAID_PATH);

        if ($minAmountToBePaid === null || $minAmountToBePaid <= 0) {
            throw new BadConfigurationException([BadConfigurationException::MISSING_CONFIG_ENTRY_KEY => 'minimum_amount_to_be_paid']);
        }

        return config(self::MIN_AMOUNT_TO_BE_PAID_PATH);
    }
}
