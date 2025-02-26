<?php

namespace App\Managers;

use App\Exceptions\SimpleValidationException;
use App\Http\Controllers\ParametersTrait;
use App\Models\FoodCategory;
use App\Services\UploadService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FoodCategoryManager
{
    use ParametersTrait;

    public function __construct()
    {
    }

    public function create(Request $request): FoodCategory
    {
        $params = $this->getParams($request, ['name', 'description', 'visibility' => 0, 'position' => 0]);
        if ($params['name'] == 'Brak kategorii') {
            throw new SimpleValidationException([__('admin.FoodCategoryManager_cannot_create_category_with_name_Brak_kategorii')]);
        }
        $references = $this->getParams($request, ['parent', 'availability']);

        ! isset($references['parent']['id']) ?: $references['parent']['id'] = $params['parent_id'] = $this->normalizeParentId($references['parent']['id']);

        $foodCategory = DB::connection('tenant')->transaction(function () use ($params, $references, $request) {
            $foodCategory = FoodCategory::create($params)->fresh();

            $uploadService = new UploadService();
            $uploadService->moveTempFiles($request, $foodCategory);

            if (isset($references['availability'])) {
                $references['availability']['food_category_id'] = $foodCategory->id;
                $foodCategory->availability()->updateOrCreate(['food_category_id' => $foodCategory->id], $references['availability']);
            }

            $foodCategory->fixTree();
            $foodCategory->parent_id = $references['parent']['id'] ?? null;
            $foodCategory->save();

            return $foodCategory;
        });

        return $foodCategory;
    }

    public function update(Request $request, FoodCategory $foodCategory): FoodCategory
    {
        $params = $this->getParams($request, ['name', 'description', 'visibility' => 0, 'parent_id' => null, 'position' => 0]);
        if ($foodCategory->name == 'Brak kategorii') {
            throw new SimpleValidationException([__('admin.FoodCategoryManager_cannot_edit_category_with_name_Brak_kategorii')]);
        }
        $references = $this->getParams($request, ['parent', 'availability']);

        ! isset($references['parent']['id']) ?: $references['parent']['id'] = $params['parent_id'] = $this->normalizeParentId($references['parent']['id']);

        DB::connection('tenant')->transaction(function () use ($params, $references, $foodCategory) {
            if (! empty($params)) {
                $foodCategory->fixTree();
                $foodCategory->parent_id = $references['parent']['id'] ?? null;
                $foodCategory->update($params);
                $foodCategory->fresh();
            }

            if (isset($references['availability'])) {
                $references['availability']['food_category_id'] = $foodCategory->id;
                $foodCategory->availability()->updateOrCreate(['food_category_id' => $foodCategory->id], $references['availability']);
            }
        });

        return $foodCategory;
    }

    public function delete(FoodCategory $foodCategory): FoodCategory
    {
        if ($foodCategory->name == 'Brak kategorii') {
            throw new SimpleValidationException([__('admin.FoodCategoryManager_cannot_remove_category_with_name_Brak_kategorii')]);
        }
        DB::connection('tenant')->table('food_categories')->where('id', $foodCategory->id)->delete();

        DB::connection('tenant')->table('promotions')->where('food_category_id', $foodCategory->id)->update(['active' => 0]);

        return $foodCategory;
    }

    /**
     * -1 is a special value for "do not assign category".
     */
    private function normalizeParentId(int $value): ?int
    {
        return $value == -1 ? null : $value;
    }
}
