<?php

namespace App\Console\Commands;

use App\Models\AdditionGroupCategory;
use App\Models\Dish;
use App\Models\FoodCategory;
use App\Models\Promotion;
use App\Models\Restaurant;
use App\Repositories\MultiTentantRepositoryTrait;
use App\Repositories\RestaurantRepository;
use Hyn\Tenancy\Environment;
use Hyn\Tenancy\Facades\TenancyFacade;
use Hyn\Tenancy\Models\Website;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class FixFoodCategoryDuplicates extends Command
{
    use MultiTentantRepositoryTrait;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'restaurants:fix-category-duplicates {restaurantIdOrName? : The ID or name of the restaurant}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Restaurants - fix category duplicates, can pass restaurantId or restaurantName';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        try {
            $this->info('<fg=magenta>Starting to fix category duplicates for restaurants... </>', 'magenta');

            $restaurantIdOrName = $this->argument('restaurantIdOrName');

            if ($restaurantIdOrName) {
                if (is_numeric($restaurantIdOrName)) {
                    $restaurant = Restaurant::findOrFail($restaurantIdOrName);
                } else {
                    $restaurant = Restaurant::where('name', 'LIKE', $restaurantIdOrName)->firstOrFail();
                }

                $this->fixDuplicates($restaurant);
                $this->info('Fixed duplicates for restaurant: ' . $restaurant->name);
            } else {
                $restaurants = Restaurant::all();

                foreach ($restaurants as $restaurant) {
                    $this->fixDuplicates($restaurant);
                    $this->info('Fixed duplicates for restaurant: <fg=cyan>' . $restaurant->name . '</>', 'cyan');
                }
            }

            $this->info('<fg=magenta>Successfully fixed all category duplicates. </>', 'magenta');
        } catch (\Throwable $e) {
            Log::error($e->getMessage());

            $restaurantInfo = $restaurantIdOrName ? 'with ID or name: ' . $restaurantIdOrName : 'not specified';
            $this->error('Error for restaurant ' . $restaurantInfo . ', ' . $e->getMessage());

            throw new \Exception("Error fixing duplicates: " . $e->getMessage());
        }
    }

    private function fixDuplicates(Restaurant $restaurant)
    {
        $this->reconnect($restaurant);
        $this->info('Processing restaurant:  <fg=cyan>' . $restaurant->name . '</>', 'cyan');
        DB::beginTransaction();
        try {
            $foodCategories = FoodCategory::all();
            $groupedCategories = $foodCategories->groupBy('name');
            if ($this->moveDishes($groupedCategories) &&
                $this->moveAdditionGroups($groupedCategories) &&
                $this->movePromotions($groupedCategories) &&
                $this->moveParentCategory($groupedCategories)) {
                $this->lockNoCategory($groupedCategories);
                $this->deleteDuplicateCategories($groupedCategories);
                $this->info('Successfully fixed duplicates for restaurant:  <fg=cyan>' . $restaurant->name . '</>', 'cyan');
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error fixing duplicates for restaurant ' . $restaurant->name . ': ' . $e->getMessage());
            throw new \Exception("Error fixing duplicates: " . $e->getMessage());
        }
    }

    private function lockNoCategory(Collection $groupedCategories)
    {
        try {
            foreach ($groupedCategories as $name => $categories) {
                if ($name == 'Brak kategorii') {
                    foreach ($categories as $category) {
                        if($category->is_editable || $category->is_inheritable) {
                            $this->info('Locking category: ' . $category->name . ' with id: ' . $category->id );
                            $category->is_editable = false;
                            $category->is_inheritable = false;
                            $category->visibility = false;
                            $category->save();
                        }
                    }
                }
            }
        }
        catch (\Exception $e) {
            $this->error('Error locking No category: ' . $e->getMessage());
            Log::error('Error locking No category: ' . $e->getMessage());
            throw new \Exception("Error locking No category: " . $e->getMessage());
        }

    }

    private function moveDishes(Collection $groupedCategories)
    {
        $success = true;

        try {
            foreach ($groupedCategories as $name => $categories) {
                if ($categories->count() > 1) {
                    $this->warn('Found duplicate category: ' . $name . ' with ' . $categories->count() . ' categories.');

                    $categoriesArray = $categories->all();
                    $primaryCategory = array_shift($categoriesArray);

                    foreach ($categoriesArray as $duplicateCategory) {
                        $dishesCount = Dish::where('food_category_id', $duplicateCategory->id)->count();
                        if ($dishesCount > 0) {
                            $movedDishesCount = Dish::where('food_category_id', $duplicateCategory->id)
                                ->update(['food_category_id' => $primaryCategory->id]);

                            $this->info('Moved ' . $movedDishesCount . ' dishes from category ' . $duplicateCategory->id . ' to ' . $primaryCategory->id);

                            $remainingDishesCount = Dish::where('food_category_id', $duplicateCategory->id)->count();
                            if ($remainingDishesCount > 0) {
                                Log::warning('Could not move all dishes from category ' . $duplicateCategory->id . ' to ' . $primaryCategory->id . ' because ' . $remainingDishesCount . ' dishes are still referencing the duplicate category.');
                                $success = false;
                            }
                        } else {
                            $this->info('No dishes found in duplicate category ' . $duplicateCategory->id);
                            return $success;
                        }
                    }

                    $dishesInPrimaryCategory = Dish::where('food_category_id', $primaryCategory->id)->count();
                    if ($dishesInPrimaryCategory === 0) {
                        $success = false;
                        Log::warning('No dishes in primary category after move for category ' . $primaryCategory->id);
                    }
                }
            }

            return $success;
        } catch (\Exception $ex) {
            $this->error('Failed to move dishes from category ' . $duplicateCategory->id . ' to ' . $primaryCategory->id . ' reason: ' . $ex->getMessage());
            Log::error('Failed to move dishes from category ' . $duplicateCategory->id . ' to ' . $primaryCategory->id . ' reason: ' . $ex->getMessage());
            return false;
        }
    }

    private function moveAdditionGroups(\Illuminate\Database\Eloquent\Collection $groupedCategories)
    {
        try {
            foreach ($groupedCategories as $name => $categories) {
                if ($categories->count() > 1) {
                    $primaryCategory = $categories->first();
                    foreach ($categories->slice(1) as $duplicateCategory) {
                        $movedAdditionGroupsCount = AdditionGroupCategory::where('category_id', $duplicateCategory->id)
                            ->update(['category_id' => $primaryCategory->id]);

                        $remainingAdditionGroupsCount = AdditionGroupCategory::where('category_id', $duplicateCategory->id)->count();
                        if ($remainingAdditionGroupsCount > 0) {
                            Log::warning('Could not move addition groups from category ' . $duplicateCategory->id . ' to ' . $primaryCategory->id . ' because it still has ' . $remainingAdditionGroupsCount . ' referenced addition groups.');
                            return false;
                        }
                    }
                }
            }
            return true;
        } catch (\Exception $ex) {
            Log::error('Failed to move addition groups from category ' . $duplicateCategory->id . ' to ' . $primaryCategory->id . ' reason: ' . $ex->getMessage());
            return false;
        }
    }


    private function movePromotions(Collection $groupedCategories)
    {
        try {
            foreach ($groupedCategories as $name => $categories) {
                if ($categories->count() > 1) {
                    $primaryCategory = $categories->first();
                    foreach ($categories->slice(1) as $duplicateCategory) {
                        Promotion::where('order_category_id', $duplicateCategory->id)
                            ->update(['order_category_id' => $primaryCategory->id]);

                        $remainingPromotionsCount = Promotion::where('order_category_id', $duplicateCategory->id)->count();
                        if ($remainingPromotionsCount > 0) {
                            Log::warning('Could not move promotions from category ' . $duplicateCategory->id . ' to ' . $primaryCategory->id . ' because it still has ' . $remainingPromotionsCount . ' referenced promotions.');
                            return false;
                        }
                    }
                }
            }
            return true;
        } catch (\Exception $ex) {
            Log::error('Failed to move promotions from category ' . $duplicateCategory->id . ' to ' . $primaryCategory->id . ' reason: ' . $ex->getMessage());
            return false;
        }
    }

    private function moveParentCategory(Collection $groupedCategories)
    {
        try {
            $groupedCategories->each(function ($categories) {
                if ($categories->count() > 1) {
                    $primaryCategory = $categories->first();
                    foreach ($categories->slice(1) as $duplicateCategory) {
                        $movedCategoriesCount = FoodCategory::where('parent_id', $duplicateCategory->id)
                            ->update(['parent_id' => $primaryCategory->id]);

                        $remainingCategoriesCount = FoodCategory::where('parent_id', $duplicateCategory->id)->count();
                        if ($remainingCategoriesCount > 0) {
                            Log::warning('Could not move all dishes from category ' . $duplicateCategory->id . ' to ' . $primaryCategory->id . ' because ' . $remainingCategoriesCount. ' dishes are still referencing the duplicate category.');
                            return false;
                        }
                    }
                }
                return true;
            }
            );
            return true;
        }
        catch (\Exception $ex) {
            Log::error('Failed to move dishes from category reason: ' . $ex->getMessage());
            return false;
        }
    }

    private function deleteDuplicateCategories(\Illuminate\Database\Eloquent\Collection $groupedCategories)
    {
        try {
            foreach ($groupedCategories as $name => $categories) {
                if ($categories->count() > 1) {
                    foreach ($categories->slice(1) as $duplicateCategory) {
                        $remainingDishesCount = Dish::where('food_category_id', $duplicateCategory->id)->count();
                        $remainingAdditionGroupsCount = AdditionGroupCategory::where('category_id', $duplicateCategory->id)->count();
                        $remainingPromotionsCount = Promotion::where('order_category_id', $duplicateCategory->id)->count();

                        if ($remainingDishesCount === 0 && $remainingAdditionGroupsCount === 0 && $remainingPromotionsCount === 0) {
                            try {
                                $duplicateCategory->delete();
                                Log::info('Successfully deleted category ' . $duplicateCategory->id);
                            } catch (\Exception $ex) {
                                Log::error('Failed to delete category ' . $duplicateCategory->id . ' reason: ' . $ex->getMessage());

                            }
                        } else {
                            Log::warning('Cannot delete category ' . $duplicateCategory->id . ' because it has ' . $remainingDishesCount . ' dishes, ' . $remainingAdditionGroupsCount . ' addition groups, and ' . $remainingPromotionsCount . ' promotions.');
                        }
                    }
                }
            }
        } catch (\Exception $ex) {
            Log::error('Failed to delete duplicate categories reason: ' . $ex->getMessage());
        }
    }
}
