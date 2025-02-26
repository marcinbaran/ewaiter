<?php

namespace App\Http\Resources\Admin;

use App\Http\Resources\ResourceTrait;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FoodCategoryResource extends JsonResource
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
            case 'dishes':
                return DishResource::collection($val);
                break;
            case 'photo':
                return $val ? new ResourceResource($val) : null;
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
            'name' => $this->getTranslation('name', config('app.locale')),
            'photo' => $this->photo,
            'parent' => ['id' => $this->parent_id],
            'description' => $this->getTranslation('description', config('app.locale')),
            'position' => $this->position,
            'name_translation' => $this->name_translation,
            'description_translation' => $this->description_translation,
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
