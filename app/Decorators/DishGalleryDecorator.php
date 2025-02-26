<?php

namespace App\Decorators;

use App\Http\Resources\Admin\FoodCategoryResource;
use App\Models\Dish;

class DishGalleryDecorator
{
    public function decorate(Dish $category)
    {
        return view(
            'admin.partials.decorators.dish-gallery',
            [
                'row' => new FoodCategoryResource($category)]
        );
    }
}
