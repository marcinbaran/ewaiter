<?php

namespace App\Managers;

use App\Http\Controllers\ParametersTrait;
use App\Models\RestaurantTag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RestaurantTagManager
{
    use ParametersTrait;

    public function createFromRequest(Request $request)
    {
        $params = $this->getParams($request);

        return self::create($params);
    }

    public static function create(array $params)
    {
        $namedParams['key'] = $params['key'];
        $namedParams['value'] = array_merge_recursive(['pl' => $params['name']], $params['name_locale'] ?? []);
        $restaurantTag = DB::transaction(function () use ($namedParams) {
            $restaurantTag = RestaurantTag::create($namedParams)->fresh();

            return $restaurantTag;
        });

        return $restaurantTag;
    }

    public function update(Request $request, RestaurantTag $restaurant_tag): RestaurantTag
    {
        $params = $request->all();
        $namedParams['key'] = $params['key'];
        $namedParams['value'] = array_merge_recursive(['pl' => $params['name']], $params['name_locale'] ?? []);

        if (! empty($namedParams)) {
            $restaurant_tag->update($namedParams);
        }

        return $restaurant_tag;
    }

    public function delete(RestaurantTag $restaurant_tag)
    {
        $restaurant_tag->delete();
    }
}
