<?php

namespace App\Services;

use App\Models\Dish;
use App\Models\Promotion;

class UtilService
{
    public static function getLocales()
    {
        $ignoreFilePath = storage_path('.ignore_locales');

        if (! \File::exists($ignoreFilePath)) {
            $ignoreLocales = [];
        } else {
            $result = json_decode(\File::get($ignoreFilePath));

            $ignoreLocales = ($result && is_array($result)) ? $result : [];
        }

        $locales = array_merge(
            [config('app.locale')],
            config('app.available_locales'),
            \Barryvdh\TranslationManager\Models\Translation::groupBy('locale')->pluck('locale')->toArray()
        );

        $locales = array_unique($locales);
        sort($locales);

        return array_values(array_diff($locales, $ignoreLocales));
    }

    public static function exchangeOldToSelect2($old, $key, $default)
    {
        $newArray = [];
        if (! empty($old) && is_array($old)) {
            foreach ($old as $item) {
                $newArray[] = [$key => $item['id']];
            }
        } else {
            $newArray = $default;
        }

        return json_encode($newArray);
    }

    public static function finalPriceForDish(Dish $dish)
    {
        $price = $dish->price;
        $promotion = self::findPromotionForDish($dish);

        if (! $promotion instanceof Promotion) {
            return $price;
        }

        switch ($promotion->type_value) {
            case 0: //percentage
                return round($price - ($price * ((100 - (float) $promotion->value) / 100)), 2);
            case 1: //amount
                return round($price - (float) $promotion->value, 2);
        }

        return $price;
    }

    public static function findPromotionForDish(Dish $dish)
    {
        return $dish->promotions->first();
    }
}
