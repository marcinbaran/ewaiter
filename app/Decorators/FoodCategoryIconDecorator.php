<?php

namespace App\Decorators;

use App\Http\Resources\Admin\FoodCategoryResource;
use App\Models\FoodCategory;

class FoodCategoryIconDecorator
{
    public function decorate(FoodCategory $category)
    {
        return view('admin.partials.decorators.food-category-icon', ['row' => new FoodCategoryResource($category)]);
    }
}
