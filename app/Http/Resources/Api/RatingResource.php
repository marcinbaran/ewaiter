<?php

namespace App\Http\Resources\Api;

use App\Http\Resources\ResourceTrait;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
/**
 * @OA\Schema(
 *     schema="RatingResource",
 *     type="object",
 *     title="Rating Resource",
 *     description="Resource representing a rating"
 * )
 */
class RatingResource extends JsonResource
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

    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     *
     * @OA\Property(
     *     property="id",
     *     type="integer",
     *     description="The ID of the rating"
     * ),
     * @OA\Property(
     *     property="restaurant_id",
     *     type="integer",
     *     description="The ID of the restaurant"
     * ),
     * @OA\Property(
     *     property="restaurant",
     *     ref="#/components/schemas/RestaurantResource",
     *     description="The restaurant being rated"
     * ),
     * @OA\Property(
     *     property="bill_id",
     *     type="integer",
     *     description="The ID of the bill associated with the rating"
     * ),
     * @OA\Property(
     *     property="anonymous",
     *     type="boolean",
     *     description="Whether the rating is anonymous"
     * ),
     * @OA\Property(
     *     property="comment",
     *     type="string",
     *     description="Comment provided by the user"
     * ),
     * @OA\Property(
     *     property="restaurant_comment",
     *     type="string",
     *     description="Comment provided by the restaurant"
     * ),
     * @OA\Property(
     *     property="r_food",
     *     type="integer",
     *     description="Rating for the food"
     * ),
     * @OA\Property(
     *     property="r_delivery",
     *     type="integer",
     *     description="Rating for the delivery"
     * ),
     * @OA\Property(
     *     property="visibility",
     *     type="boolean",
     *     description="Visibility of the rating"
     * )
     */
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'restaurant_id' => $this->restaurant_id,
            'restaurant' => new RestaurantResource($this->restaurant),
            'bill_id' => $this->bill_id,
            'anonymous' => $this->anonymous,
            'comment' => $this->comment,
            'restaurant_comment' => $this->restaurant_comment,
            'r_food' => $this->r_food,
            'r_delivery' => $this->r_delivery,
            'visibility' => $this->visibility,
        ];
    }
}
