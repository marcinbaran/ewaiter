<?php

namespace App\Http\Resources\Admin;

use App\Http\Resources\ResourceTrait;
use Illuminate\Http\Resources\Json\JsonResource;

class StatisticResource extends JsonResource
{
    use ResourceTrait;

    public function toArray($request)
    {
        $array = parent::toArray($request);
        if (isset($array['table'])) {
            $array['table_name'] = __($array['table']['name']);
            unset($array['table']);
        }
        if (isset($array['dish']['name'])) {
            $array['dish_name'] = __($array['dish']['name']);
            unset($array['dish']);
        }

        return $array;
    }
}
