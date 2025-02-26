<?php

namespace App\Http\Resources\Admin;

use App\Http\Resources\ResourceTrait;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RestaurantsRestaurantTagResource extends JsonResource
{
    use ResourceTrait;

    /**
     * @var int Default limit items per page
     */
    public const LIMIT = 20;

    public function __get($key)
    {
        $val = parent::__get($key);

        switch ($key) {
            case 'photo':
                return $val ? ResourceResource($val) : null;
                break;
            default: return $val;
                break;
        }
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
        $array = [
            'id' => $this->id,
            'key' => $this->key,
            'value' => $this->value,
            'createdAt' => $this->dateFormat($this->created_at),
            'updatedAt' => $this->dateFormat($this->updated_at),
        ];

        return $array;
    }

    /**
     * @return string
     */
    public function isVisibility(): string
    {
        return $this->visibility ? 'Yes' : 'No';
    }
}
