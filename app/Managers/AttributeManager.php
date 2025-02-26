<?php

namespace App\Managers;

use App\Http\Controllers\ParametersTrait;
use App\Models\Attribute;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AttributeManager
{
    use ParametersTrait;

    public function createFromRequest(Request $request): Attribute
    {
        $params = $this->getParams($request, ['key', 'name', 'description', 'icon', 'is_active' => 0, 'attribute_group_id']);

        return self::create($params);
    }

    public function updateFromRequest(Request $request, Attribute $attribute): Attribute
    {
        $params = $this->getParams($request, ['key', 'name', 'description', 'icon', 'is_active' => 0, 'attribute_group_id']);

        return self::update($attribute, $params);
    }

    public static function create(array $params): Attribute
    {
        $attribute = DB::connection('tenant')->transaction(function () use ($params) {
            return Attribute::create(Attribute::decamelizeArray(array_diff_key($params, ['references' => 1])))->fresh();
        });

        return $attribute;
    }

    public static function update(Attribute $attribute, array $params): Attribute
    {
        if (! empty($params)) {
            DB::connection('tenant')->transaction(function () use ($params, $attribute) {
                $attribute->update(Attribute::decamelizeArray(array_diff_key($params, ['references' => 1])));
                $attribute->fresh();
            });
        }

        return $attribute;
    }

    public static function delete(Attribute $attribute): Attribute
    {
        DB::connection('tenant')->transaction(function () use ($attribute) {
            $attribute->delete();
        });

        return $attribute;
    }
}
