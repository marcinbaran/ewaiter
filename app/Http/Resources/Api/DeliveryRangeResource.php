<?php

namespace App\Http\Resources\Api;

use App\Http\Resources\ResourceTrait;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *     schema="DeliveryRangeResource",
 *     type="object",
 *     title="Delivery Range Resource",
 *     description="Delivery Range resource representation",
 *     @OA\Property(property="id", type="integer", description="ID of the delivery range"),
 *     @OA\Property(property="name", type="string", description="Name of the delivery range"),
 *     @OA\Property(property="rangeFrom", type="integer", description="Starting range value"),
 *     @OA\Property(property="rangeTo", type="integer", description="Ending range value"),
 *     @OA\Property(property="minValue", type="number", format="float", description="Minimum order value"),
 *     @OA\Property(property="freeFrom", type="number", format="float", description="Order value from which delivery is free"),
 *     @OA\Property(property="cost", type="number", format="float", description="Delivery cost"),
 *     @OA\Property(property="kmCost", type="number", format="float", description="Cost per kilometer"),
 *     @OA\Property(property="outOfRange", type="boolean", description="Whether delivery is possible out of range")
 * )
 */
class DeliveryRangeResource extends JsonResource
{
    use ResourceTrait;

    /**
     * @var int Default limit items per page
     */
    public const LIMIT = 20;

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
