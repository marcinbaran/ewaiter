<?php

namespace App\Http\Resources\Api;

use App\Http\Resources\ResourceTrait;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
/**
 * @OA\Schema(
 *     schema="AdditionGroupDishResource",
 *     type="object",
 *     title="Addition Group Dish Resource",
 *     description="Resource representing the relationship between an addition group and a dish",
 *     @OA\Property(property="id", type="integer", example=1, description="The ID of the addition group dish relationship"),
 *     @OA\Property(property="addition_group_id", type="integer", example=1, description="The ID of the addition group"),
 *     @OA\Property(property="dish_id", type="integer", example=1, description="The ID of the dish"),
 *     @OA\Property(
 *         property="addition_group",
 *         ref="#/components/schemas/AdditionGroupResource",
 *         description="The addition group associated with the dish"
 *     )
 * )
 */
class AdditionGroupDishResource extends JsonResource
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
        $array = [
            'id' => $this->id,
            'addition_group_id' => $this->addition_group_id,
            'dish_id' => $this->addition_group_id,
            'addition_group' => new AdditionGroupResource($this->addition_group),
        ];

        return $array;
    }
}
