<?php

namespace App\Helpers;

use App\Enum\PromotionType;
use App\Enum\PromotionValueType;
use App\Models\Dish;
use App\Models\Promotion;

class PromotionHelper
{
    public const string NAME_KEY = 'name';

    public const string PRICE_KEY = 'price';

    public const string DISCOUNT_KEY = 'discount';

    public const string TYPE_KEY = 'type';

    public static function getPromotionForDish(Dish $dish, string $locale = 'pl'): ?array
    {
        $dishPromotion = $dish->promotions->first();
        $categoryPromotion = $dish->promotions_category->first();

        $promotion = null;

        if ($dishPromotion) {
            if ($dishPromotion->merge && $categoryPromotion) {
                $promotion = self::getPromotionObject(
                    $dishPromotion->getTranslation('name', $locale),
                    PromotionType::MERGE,
                    $dish->price,
                    self::calculatePromotionsMergePrice($dishPromotion, $categoryPromotion, $dish->price)
                );
            } else {
                $promotion = self::getPromotionObject(
                    $dishPromotion->getTranslation('name', $locale),
                    PromotionType::from($dishPromotion->type),
                    $dish->price,
                    self::calculatePromotionPrice($dishPromotion, $dish->price)
                );
            }
        } elseif ($categoryPromotion) {
            $promotion = self::getPromotionObject(
                $categoryPromotion->getTranslation('name', $locale),
                PromotionType::from($categoryPromotion->type),
                $dish->price,
                self::calculatePromotionPrice($categoryPromotion, $dish->price)
            );
        }

        return $promotion;
    }

    public static function calculatePromotionsMergePrice(Promotion $dishPromotion, Promotion $categoryPromotion, float $dishPrice): float
    {
        $dishPromotionValue = self::calculatePromotionPrice($dishPromotion, $dishPrice);

        return self::calculatePromotionPrice($categoryPromotion, $dishPromotionValue);
    }

    public static function calculatePromotionPrice(Promotion $promotion, float $dishPrice): float
    {
        $type = PromotionValueType::from($promotion->type_value);

        if ($type === PromotionValueType::PERCENTAGE) {
            return round($dishPrice - ($dishPrice * ((float) $promotion->value) / 100), 2);
        } elseif ($type === PromotionValueType::PRICE) {
            return round($dishPrice - (float) $promotion->value, 2);
        }

        return $dishPrice;
    }

    public static function getPromotionObject(string $name, PromotionType $type, float $oldPrice, float $newPrice): array
    {
        $amount = round($oldPrice - $newPrice, 2);
        $percentage = floor(($amount / $oldPrice) * 100);

        return [
            self::NAME_KEY => $name,
            self::PRICE_KEY => [
                'original' => $oldPrice,
                'discounted' => $newPrice,
            ],
            self::DISCOUNT_KEY => [
                'percentage' => $percentage,
                'amount' => $amount,
            ],
            self::TYPE_KEY => $type->value,
        ];
    }

    public static function isPromotionValid(float $price): bool
    {
        $minDishPrice = config('app.minimal_dish_price', 1);

        return $price >= $minDishPrice;
    }
}
