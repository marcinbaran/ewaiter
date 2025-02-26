<?php

namespace App\Http\Resources\Admin;

use App\Http\Resources\PhotoTrait;
use App\Http\Resources\ResourceTrait;
use App\Models\Resource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DishResource extends JsonResource
{
    use ResourceTrait,
        PhotoTrait;

    /**
     * @var int Default limit items per page
     */
    public const LIMIT = 20;

    public function __get($key)
    {
        $val = parent::__get($key);
        switch ($key) {
            case 'category':
                return new FoodCategoryResource($val);
                break;
            case 'photos':
                return ResourceResource::collection($val);
                break;
            case 'additions':
                return AdditionResource::collection($val);
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
        return [
            'id' => $this->id,
            'name' => $this->getTranslation('name', config('app.locale')),
            'description' => $this->getTranslation('description', config('app.locale')),
            'category' => $this->category,
            'price' => $this->price,
            'timeWait' => $this->time_wait,
            'photos' => $this->photos,
            'delivery' => $this->delivery,
            'position' => $this->position,
        ];
    }

    /**
     * @return string
     */
    public function isVisibility(): string
    {
        return $this->visibility ? 'Yes' : 'No';
    }

    /**
     * @return string
     */
    public function isDelivery(): string
    {
        return $this->visibility ? 'Yes' : 'No';
    }

    /**
     * @return string
     */
    public function getTagsNameTranslateAsString()
    {
        if (count($this->tags)) {
            $tag_names = [];
            foreach ($this->tags as $tag) {
                $tag_names[] = gtrans('tags.'.$tag->tag->name);
            }

            return implode(',', $tag_names);
        } else {
            return '';
        }
    }
}
