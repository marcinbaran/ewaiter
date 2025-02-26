<?php

namespace App\Managers;

use App\Http\Controllers\ParametersTrait;
use App\Models\Attribute;
use App\Models\AttributeGroup;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AttributeGroupManager
{
    use ParametersTrait;

    public function createFromRequest(Request $request): AttributeGroup
    {
        $params = $this->getParams($request, ['key', 'name', 'description', 'input_type', 'is_primary' => 0, 'is_active' => 0, 'attribute_ids' => null]);

        return self::create($params);
    }

    public function updateFromRequest(Request $request, AttributeGroup $attributeGroup): AttributeGroup
    {
        $params = $this->getParams($request, ['key', 'name', 'description', 'input_type', 'is_primary' => 0, 'is_active' => 0]);

        return self::update($attributeGroup, $params);
    }

    public static function create(array $params): AttributeGroup
    {
        $attributeGroup = DB::connection('tenant')->transaction(function () use ($params) {
            return AttributeGroup::create(AttributeGroup::decamelizeArray(array_diff_key($params, ['references' => 1])))->fresh();
        });

        if (! empty($params['attribute_ids'])) {
            $attributeIds = explode(',', $params['attribute_ids']);
            foreach ($attributeIds as $attributeId) {
                $attribute = Attribute::find($attributeId);
                if ($attribute) {
                    $attribute->update(['attribute_group_id' => $attributeGroup->id]);
                }
            }
        }

        return $attributeGroup;
    }

    public static function update(AttributeGroup $attributeGroup, array $params): AttributeGroup
    {
        if (! empty($params)) {
            DB::connection('tenant')->transaction(function () use ($params, $attributeGroup) {
                $attributeGroup->update(AttributeGroup::decamelizeArray(array_diff_key($params, ['references' => 1])));
                $attributeGroup->fresh();
            });
        }

        return $attributeGroup;
    }

    public static function delete(AttributeGroup $attributeGroup): AttributeGroup
    {
        DB::connection('tenant')->transaction(function () use ($attributeGroup) {
            $attributeGroup->delete();
        });

        return $attributeGroup;
    }
}
