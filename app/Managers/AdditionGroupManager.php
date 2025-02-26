<?php

namespace App\Managers;

use App\Http\Controllers\ParametersTrait;
use App\Models\AdditionAdditionGroup;
use App\Models\AdditionGroup;
use App\Models\AdditionGroupCategory;
use App\Models\AdditionGroupDish;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdditionGroupManager
{
    use ParametersTrait;

    /**
     * @param Request $request
     *
     * @return AdditionGroup
     */
    public function createFromRequest(Request $request): AdditionGroup
    {
        $params = $this->getParams($request, ['name', 'type', 'visibility' => 0, 'mandatory']);
        $params['references'] = $this->getParams($request, ['addition_group_category', 'addition_group_dish']);

        return self::create($params);
    }

    /**
     * @param Request $request
     * @param AdditionGroup $addition_group
     *
     * @return AdditionGroup
     */
    public function updateFromRequest(Request $request, AdditionGroup $addition_group): AdditionGroup
    {
        $params = $this->getParams($request, ['name', 'mandatory', 'type', 'visibility' => 0]);
        $params['references'] = $this->getParams($request, ['addition_group_category', 'addition_group_dish']);

        return self::update($addition_group, $params);
    }

    public function duplicateGroup(Request $request, AdditionGroup $additionGroup)
    {
        return DB::transaction(function () use ($request, $additionGroup) {
            $newGroup = $additionGroup->replicate();
            $newName = $additionGroup->name.' (copy)';
            $i = 1;
            while (AdditionGroup::query()->where('name', $newName)->count() > 0) {
                $newName = $additionGroup->name.' (copy '.$i.')';
                $i++;
            }
            $newGroup->name = $newName;
            $newGroup->save();

            foreach ($additionGroup->additions_additions_groups()->get() as $additionAdditionGroup) {
                AdditionAdditionGroup::create([
                    'addition_id' => $additionAdditionGroup->addition_id,
                    'addition_group_id' => $newGroup->id,
                ]);
            }

            foreach ($additionGroup->additions_groups_categories()->get() as $category) {
                AdditionGroupCategory::create([
                    'category_id' => $category->category_id,
                    'addition_group_id' => $newGroup->id,
                ]);
            }

            foreach ($additionGroup->additions_groups_dishes()->get() as $dish) {
                AdditionGroupDish::create([
                    'dish_id' => $dish->dish_id,
                    'addition_group_id' => $newGroup->id,
                ]);
            }

            return $newGroup;
        });
    }

    /**
     * @param AdditionGroup $addition_group
     *
     * @return AdditionGroup
     */
    public static function delete(AdditionGroup $addition_group): AdditionGroup
    {
        DB::connection('tenant')->transaction(function () use ($addition_group) {
            $addition_group->delete();
        });

        return $addition_group;
    }

    /**
     * @param array $params
     *
     * @return AdditionGroup
     */
    public static function create(array $params): AdditionGroup
    {
        $addition_group = DB::connection('tenant')->transaction(function () use ($params) {
            $addition_group = AdditionGroup::create(AdditionGroup::decamelizeArray(array_diff_key($params, ['references' => 1])))->fresh();

            if (isset($params['references']['addition_group_category'])) {
                foreach ($params['references']['addition_group_category'] as $addition_group_category) {
                    $addition_group->additions_groups_categories()->create(['category_id' => (int) $addition_group_category['id'], 'addition_group_id' => $addition_group->id]);
                }
            }
            if (isset($params['references']['addition_group_dish'])) {
                foreach ($params['references']['addition_group_dish'] as $addition_group_dish) {
                    $addition_group->additions_groups_dishes()->create(['dish_id' => (int) $addition_group_dish['id'], 'addition_group_id' => $addition_group->id]);
                }
            }

            return $addition_group;
        });

        return $addition_group;
    }

    /**
     * @param AdditionGroup $addition_group
     * @param array $params
     *
     * @return AdditionGroup
     */
    public static function update(AdditionGroup $addition_group, array $params): AdditionGroup
    {
        if (! empty($params)) {
            DB::connection('tenant')->transaction(function () use ($params, $addition_group) {
                $addition_group->update(AdditionGroup::decamelizeArray(array_diff_key($params, ['references' => 1])));
                $addition_group->fresh();

                $addition_group->additions_groups_categories()->delete();
                if (isset($params['references']['addition_group_category'])) {
                    foreach ($params['references']['addition_group_category'] as $addition_group_category) {
                        $addition_group->additions_groups_categories()->create(['category_id' => (int) $addition_group_category['id'], 'addition_group_id' => $addition_group->id]);
                    }
                }
                $addition_group->additions_groups_dishes()->delete();
                if (isset($params['references']['addition_group_dish'])) {
                    foreach ($params['references']['addition_group_dish'] as $addition_group_dish) {
                        $addition_group->additions_groups_dishes()->create(['dish_id' => (int) $addition_group_dish['id'], 'addition_group_id' => $addition_group->id]);
                    }
                }
            });
        }

        return $addition_group;
    }
}
