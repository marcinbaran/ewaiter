<?php

namespace App\Services;

use App\Http\Requests\Api\FoodCategoryRequest;
use App\Http\Resources\Api\DishResource;
use App\Http\Resources\Api\FoodCategoryResource;
use App\Models\Dish;
use App\Models\FoodCategory;
use App\Models\Restaurant;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;

class AllWithDishesService
{

    public function __construct(
        private BundleService $bundleService
    ) {}

    public function getAllWithDishes()
    {
        $restaurant = Restaurant::getCurrentRestaurant();
        Cache::forget('all-with-dishes_'.$restaurant->id);
        $response = Cache::remember('all-with-dishes_'.$restaurant->id, now()->addMinutes(15), function () {
            $response = [];

            $foodCategories = FoodCategoryResource::collection($this->getFilteredFoodCategories());
            $this->prependPromotions($foodCategories);

            if ($foodCategories->count()) {
                $response['food_categories'] = $foodCategories;
            }

            $bundles = $this->bundleService->getBundlesInFakeCategory();
            if (count($bundles['bundles']) > 0) {
                $response['bundles'] = $bundles;
            }

            return $response;
        });

        return response()->json($response);
    }

    private function prependPromotions(AnonymousResourceCollection $foodCategories)
    {
        $dishes = Dish::where('visibility', true)
            ->whereHas('promotions')
            ->orWhereHas('promotions_category')
            ->orderBy('position')
            ->get()
            ->filter(function ($dish) {
                return $dish->isAvailableNow();
            });

        if ($dishes->count() > 0) {
            $dishesResource = DishResource::collection($dishes);

            $promotions = collect([
                'id' => 0,
                'NumberOfDishes' => $dishes->count(),
                'numberOfChildren' => 0,
                'name' => 'Promocje',
                'parent_id' => null,
                'children' => [],
                'dishes' => $dishesResource,
            ]);

            $foodCategories->prepend($promotions);
        }
    }

    private function getFilteredFoodCategories()
    {
        $foodCategories = FoodCategory::with(['dishes'])
            ->where('parent_id', null)
            ->get()
            ->filter(function ($category) {
                return $category->name !== 'Brak kategorii';
            });

        return $foodCategories;
    }
}
