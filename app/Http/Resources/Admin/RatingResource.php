<?php

namespace App\Http\Resources\Admin;

use App\Http\Resources\ResourceTrait;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RatingResource extends JsonResource
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
