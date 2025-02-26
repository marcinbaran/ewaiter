<?php

namespace App\Http\Resources\Admin;

use App\Http\Resources\ResourceTrait;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DeliveryRangeResource extends JsonResource
{
    use ResourceTrait;

    /**
     * @var int Default limit items per page
     */
    public const LIMIT = 20;

    public function __get($key)
    {
        $val = parent::__get($key);

        return $val;
    }

    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     *
     * @return array
     */
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'rangeFrom' => $this->range_from,
            'rangeTo' => $this->range_to,
            'minValue' => $this->min_value,
            'freeFrom' => $this->free_from,
            'cost' => $this->cost,
            'kmCost' => $this->km_cost,
            'outOfRange' => $this->out_of_range,
        ];
    }
}
