<?php

namespace App\Http\Resources\Api;

use App\Http\Resources\ResourceTrait;
use Illuminate\Http\Resources\Json\JsonResource;
/**
 * @OA\Schema(
 *     schema="PromotionBundleResource",
 *     type="object",
 *     title="Promotion Bundle Resource",
 *     description="Resource representing a promotion bundle containing dishes",
 * )
 */
class PromotionBundleResource extends JsonResource
{
    use ResourceTrait;

    /**
     * @var int Default limit items per page
     */
    public const LIMIT = 20;
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     *
     * @OA\Property(
     *     property="promotion_dishes",
     *     type="array",
     *     @OA\Items(ref="#/components/schemas/DishResource"),
     *     description="List of dishes in the promotion bundle."
     * )
     */
    public function toArray($request)
    {
        $array = [];
        foreach ($this->resource as $promotion_dish) {
            $array[] = [
                'dish' => new DishResource($promotion_dish->dish),
            ];
        }

        return $array;
    }
}
