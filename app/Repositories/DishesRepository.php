<?php

namespace App\Repositories;

use App\Models\Availability;
use App\Models\Dish;
use App\Models\FoodCategory;
use App\Models\Restaurant;

class DishesRepository
{
    use MultiTentantRepositoryTrait;

    public function getDishesForRestaurant(Restaurant $restaurant)
    {
        $this->reconnect($restaurant);

        $allCategories = FoodCategory::orderBy('parent_id', 'asc')->orderBy('id', 'asc')->get();
        $allDishes = Dish::where('deleted_at', null)->with('category')->with('photos')->with('promotions')->get();
        $allAvailabilities = Availability::get();

        foreach ($allAvailabilities as $availability) {
            if ($currentDishId = $availability->dish_id) {
                self::setVisibility($allDishes, $currentDishId, $availability);
            } elseif ($currentFoodCategoryId = $availability->food_category_id) {
                self::setVisibility($allCategories, $currentFoodCategoryId, $availability);
            }
        }

        for ($i = 0; $i < $allCategories->count(); $i++) {
            if ($parentId = $allCategories[$i]->parent_id) {
                $parent = $allCategories->first(function ($value, $key) use ($parentId) {
                    return $value->id == $parentId;
                });

                $allCategories[$i]->visibility &= $parent ? $parent->visibility : 0;
            }
        }

        foreach ($allDishes as $currentDish) {
            if ($foodCategoryId = $currentDish->food_category_id) {
                $foodCategory = $allCategories->first(function ($value, $key) use ($foodCategoryId) {
                    return $value->id == $foodCategoryId;
                });
                $currentDish->visibility &= $foodCategory ? $foodCategory->visibility : false;
            }
        }

        $newDishes = $allDishes->where('visibility', true);

        $dishes = [];
        foreach ($newDishes as $newDish) {
            $dish['id'] = $newDish->id;
            $dish['food_category_id'] = $newDish->category?->id;
            $dish['name'] = $newDish->name;
            $dish['description'] = $newDish->description;
            $dish['price'] = $newDish->price;
            $dish['time_wait'] = $newDish->time_wait;
            $dish['delivery'] = $newDish->delivery;
            $dish['created_at'] = (string) $newDish->created_at;
            $dish['name_translation'] = $newDish->name;
            $dish['description_translation'] = $newDish->description;
            $dish['filename'] = ! $newDish->photos->isEmpty() ? $newDish->photos->first()->filename : null;
            $dish['promotion_type'] = ! $newDish->promotions->isEmpty() ? $newDish->promotions->first()->type : null;
            $dish['promotion_value'] = ! $newDish->promotions->isEmpty() ? (string) $newDish->promotions->first()->type_value : null;

            $dishes[] = $dish;
        }

        $fallbackLocale = config('app.fallback_locale');
        foreach ($dishes as $dish) {
            $dish['name_translation'] = (json_decode($dish['name_translation']))->$locale ??
                (json_decode($dish['name_translation']))->$fallbackLocale ??
                $dish['name'];
            $dish['description_translation'] = (json_decode($dish['description_translation']))->$locale ??
                (json_decode($dish['description_translation']))->fallbackLocale ??
                $dish['description'];
        }

        $result = [];
        foreach ($dishes as $dish) {
            if (in_array($dish['food_category_id'], $this->foodCategories)) {
                $result[] = $dish;
            }
        }

        return $result;
    }
}
