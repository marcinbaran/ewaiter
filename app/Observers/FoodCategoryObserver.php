<?php

namespace App\Observers;

use App\Events\ChangeLogs;
use App\Exceptions\NoCategoryCategoryNotFoundException;
use App\Exceptions\SimpleValidationException;
use App\Models\Dish;
use App\Models\FoodCategory;
use App\Services\UploadService;
use Illuminate\Support\Facades\Log;

class FoodCategoryObserver
{
    /**
     * @param FoodCategory $model
     */
    public function creating(FoodCategory $model)
    {
    }

    /**
     * @param FoodCategory $model
     */
    public function created(FoodCategory $model)
    {
        event(new ChangeLogs($model, 'created'));
    }

    /**
     * @param FoodCategory $model
     */
    public function updating(FoodCategory $model)
    {
    }

    /**
     * @param FoodCategory $model
     */
    public function updated(FoodCategory $model)
    {
        event(new ChangeLogs($model, 'updated'));
    }

    /**
     * @param FoodCategory $model
     */
    public function saving(FoodCategory $model)
    {
    }

    /**
     * @param FoodCategory $model
     */
    public function saved(FoodCategory $model)
    {
    }

    /**
     * @param FoodCategory $model
     */
    public function deleting(FoodCategory $model)
    {
        if (strpos($model->name, 'Brak kategorii') !== false) {
            $categories = FoodCategory::where('name', 'LIKE', '%Brak kategorii%')->get();

            if ($categories->count() <= 1) {
                Log::error('Cannot delete the only "Brak kategorii" category. There must be at least one.');
                throw new SimpleValidationException([__('admin.FoodCategoryManager_cannot_remove_only_category_with_name_Brak_kategorii')]);
            }

            $categoryToTransferDishes = $categories->first(function ($category) use ($model) {
                return $category->id !== $model->id;
            });

            if (!$categoryToTransferDishes) {
                Log::error('No other category found to move dishes to.');
                throw new SimpleValidationException([__('admin.FoodCategoryManager_no_other_category_to_move_dishes')]);
            }
        }

        $noCategoryCategory = FoodCategory::where('name', 'LIKE', '%Brak kategorii%')->first();
        if (! $noCategoryCategory) {
            throw new NoCategoryCategoryNotFoundException();
        }
        $model->dishes()->each(function (Dish $dish) use ($noCategoryCategory) {
            $dish->food_category_id = $noCategoryCategory->id;
            $dish->save();
        });
        (new UploadService())->removeFile('food_category', $model->id);
    }

    /**
     * @param FoodCategory $model
     */
    public function deleted(FoodCategory $model)
    {
        $model->photo()->delete();
        event(new ChangeLogs($model, 'deleted'));
    }

    /**
     * @param FoodCategory $model
     */
    public function restoring(FoodCategory $model)
    {
    }

    /**
     * @param FoodCategory $model
     */
    public function restored(FoodCategory $model)
    {
    }

    /**
     * @param FoodCategory $model
     */
    public function retrieved(FoodCategory $model)
    {
    }
}
