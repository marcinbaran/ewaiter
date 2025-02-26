<?php

namespace App\Actions\Assistant;

use App\Http\Resources\Api\AdditionGroupDishResource;
use App\Http\Resources\Api\AdditionResource;
use App\Models\AdditionAdditionGroup;
use App\Models\Dish;
use App\Models\DishTag;
use App\Models\FoodCategory;
use App\Models\Label;
use App\Models\Promotion;
use App\Models\Restaurant;
use App\Models\RestaurantTag;
use App\Models\Settings;
use App\Repositories\MultiTentantRepositoryTrait;

class ExportAction
{
    use MultiTentantRepositoryTrait;

    public function exportAllRestaurants()
    {
        $restaurants = [];
        foreach (Restaurant::query()->where('visibility', 1)->get() as $restaurant) {
            $restaurants[$restaurant->id] = $this->exportRestaurant($restaurant);
        }

        return $restaurants;
    }

    public function exportRestaurant(Restaurant $restaurant)
    {
        $this->reconnect($restaurant);
        $restaurantArr = $this->prepareRestaurantDetails($restaurant);
        foreach (FoodCategory::all() as $category) {
            $restaurantArr['food_categories'][] = $this->prepareCategoryDetails($category);
            foreach ($category->dishes as $dish) {
                $restaurantArr['dishes'][] = $this->prepareDishDetails($dish);
            }
        }

        foreach (Promotion::all() as $promotion) {
            $restaurantArr['promotions'][] = $this->preparePromotionDetails($promotion);
        }

        return $restaurantArr;
    }

    private function prepareRestaurantDetails(Restaurant $restaurant)
    {
        return [
            'id' => $restaurant->id,
            'restaurant_name' => $restaurant->name,
            //'restaurant' => Settings::where('key', 'restauracja')->first()->value,
            'contact' => Settings::where('key', 'kontakt')->first()->value,
            'opening_hours' => Settings::where('key', 'czas_pracy')->first()->value,
            'address' => $restaurant->address_system->city.', '.$restaurant->address_system->street.' '.$restaurant->address_system->building_number,
//            'type_of_cousine' => RestaurantTag::whereHas('restaurant_restaurant_tags', function ($query) use ($restaurant) {
//                $query->where('restaurant_id', $restaurant->id);
//            })->pluck('value')->toArray(),
        ];
    }

    private function prepareCategoryDetails(FoodCategory $category)
    {
        return [
            'food_category_id' => $category->id,
            'food_category_name' => $category->name,
//            'visibility' => $category->visibility,
//            'parent_id' => $category->parent_id,
//            'description' => $category->description,
//            'additions_groups' => $this->getDishCategoryAdditionGroups($category),
        ];
    }

    private function prepareDishDetails(Dish $dish)
    {
        return [
            'dish_id' => $dish->id,
            'dish_name' => $dish->name,
            'dish_price' => $dish->price,
            'dish_description' => $dish->description,
            'dish_visibility' => $dish->visibility,
//            'allergens' => $dish->tags->map(function (DishTag $dishTag) {
//                return $dishTag->tag->value;
//            })->toArray(),
//            'tags' => $dish->labels->map(function (Label $dishLabel) {
//                return $dishLabel->name;
//            })->toArray(),
//            'additions' => $dish->additions->map(function (AdditionAdditionGroup $additionDish) {
//                return new AdditionResource($additionDish->addition);
//            })->toArray(),
            'additions_groups' => $this->getDishAdditionGroups($dish),
            'food_category_id' => $dish->category->id,
        ];
    }

    private function getDishAdditionGroups(Dish $dish)
    {
        $mandatory = [];
        $notMandatory = [];
        foreach ($dish->additions_groups_dishes as $addition_group_dish) {
            if ($addition_group_dish->addition_group->visibility && $addition_group_dish->addition_group->additions_additions_groups->count() > 0) {
                if ($addition_group_dish->addition_group->mandatory) {
                    $mandatory[$addition_group_dish->id] = new AdditionGroupDishResource($addition_group_dish);
                } else {
                    $notMandatory[$addition_group_dish->id] = new AdditionGroupDishResource($addition_group_dish);
                }
            }
        }

        return $mandatory + $notMandatory;
    }

    private function getDishCategoryAdditionGroups(FoodCategory $foodCategory)
    {
        $mandatory = [];
        $notMandatory = [];

        foreach ($foodCategory->additions_groups_categories as $addition_group_dish) {
            if ($addition_group_dish->addition_group->visibility && $addition_group_dish->addition_group->additions_additions_groups->count() > 0) {
                if ($addition_group_dish->addition_group->mandatory) {
                    $mandatory[$addition_group_dish->id] = new AdditionGroupDishResource($addition_group_dish);
                } else {
                    $notMandatory[$addition_group_dish->id] = new AdditionGroupDishResource($addition_group_dish);
                }
            }
        }

        return $mandatory + $notMandatory;
    }

    private function preparePromotionDetails(Promotion $promotion)
    {
        return [
            'id' => $promotion->id,
            'name' => $promotion->name,
            'description' => $promotion->description,
            'price' => $promotion->price,
            'visibility' => $promotion->active,
            'type' => $promotion->type,
            'type_value' => $promotion->type_value,
        ];
    }
}
