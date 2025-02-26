<?php

namespace App\Rules\DeliveryRange;

use App\Helpers\PolygonHelper;
use App\Models\Restaurant;
use App\Services\GeoServices\GeoService;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class RestaurantOutsideTheZone implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $polygonPoints = json_decode($value);

        if (json_last_error() !== JSON_ERROR_NONE) {
            $fail(__('validation.invalid_polygon_format'));
            return;
        }

        $restaurant = Restaurant::getCurrentRestaurant();
        $restaurantCords = app(GeoService::class)->getCoordsForRestaurant($restaurant);

        if (!$restaurantCords) {
            $fail(__('validation.delivery_range.no_restaurant_cords'));
            return;
        }

        $isPointInPolygon = PolygonHelper::isPointInPolygon($restaurantCords->toArray(), $polygonPoints);

        if (!$isPointInPolygon) {
            $fail(__('validation.delivery_range.restaurant_outside_zone'));
        }


    }
}
