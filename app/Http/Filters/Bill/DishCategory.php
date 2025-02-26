<?php

namespace App\Http\Filters\Bill;

use App\Models\FoodCategory;
use Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\Views\Filters\MultiSelectFilter;

class DishCategory
{
    public function prepare()
    {
        return
            MultiSelectFilter::make('Category')
                ->options(FoodCategory::query()->get()->pluck('name', 'id')->toArray())
                ->filter(function (Builder $builder, array $value) {
                    $builder->whereHas('category', function (Builder $builder) use ($value) {
                        $builder->whereIn('food_category_id', $value);
                    });
                });
    }
}
