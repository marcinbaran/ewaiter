<?php

namespace App\Helpers;

use App\Http\Resources\Api\AttributeResource;
use App\Http\Resources\Api\LabelResource;
use App\Models\Dish;
use App\Models\Restaurant;
use Hyn\Tenancy\Facades\TenancyFacade;

class DishHelper
{
    public static function getDishObject(
        Dish $dish,
        string $locale = 'pl',
        bool $isWithAttributes = false,
        bool $isWithPromotions = false,
        bool $isWithLabels = false,
        bool $isWithAvailability = false
    ): array {
        $dishObj = [];

        $dishObj['id'] = $dish->id;
        $dishObj['food_category_id'] = $dish->category?->id;
        $dishObj['name'] = $dish->name;
        $dishObj['description'] = $dish->description;
        $dishObj['position'] = $dish->position;
        $dishObj['price'] = $dish->price;
//        $dishObj['time_wait'] = $dish->time_wait;
        $dishObj['delivery'] = $dish->delivery;
        $dishObj['tags'] = $dish->tags;
        $dishObj['created_at'] = (string) $dish->created_at;
        $dishObj['photo_url'] = $dish->photos->isEmpty() ? self::getDefaultDishPhotoUrlFromTenant() : $dish->photos->first()->getFileUrl();
        $dishObj['category_name'] = $dish->category?->name;

        if ($isWithAttributes) {
            $dishObj['attributes'] = AttributeResource::collection($dish->attributes);
        }

        if ($isWithAvailability) {
            $dishObj['availability'] = $dish->availability;
        }

        if ($isWithPromotions) {
            $dishObj['promotion'] = PromotionHelper::getPromotionForDish($dish, $locale);
        }

        if ($isWithLabels) {
            $dishObj['labels'] = self::getLabelsForDish($dish);
        }

        return $dishObj;
    }

    public static function getLabelsForDish(Dish $dish): array
    {
        $labels = [];
        foreach ($dish->labels as $label) {
            $labels[] = new LabelResource($label);
        }

        return $labels;
    }

    public static function getDefaultDishPhotoUrlFromTenant(): string
    {
        $defaultPhotoUrl = '';

        $tenant = TenancyFacade::website();
        if ($tenant) {
            $restaurant = Restaurant::where('hostname', $tenant->uuid)->first();
            $defaultPhotoUrl = $restaurant->getDefaultDishPhoto() ? $restaurant->getDefaultDishPhoto()->getFileUrl() : '';
        }

        return $defaultPhotoUrl;
    }
}
