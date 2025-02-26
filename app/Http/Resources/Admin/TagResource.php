<?php

namespace App\Http\Resources\Admin;

use App\Http\Resources\ResourceTrait;
use Illuminate\Http\Resources\Json\JsonResource;

class TagResource extends JsonResource
{
    use ResourceTrait;

    public const LIMIT = 20;

    public function __get($key)
    {
        $val = parent::__get($key);

        return $val;
    }

    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'tag' => $this->tag,
            'name' => $this->getTranslation('name', config('app.locale')),
            'icon' => $this->icon,
            'description' => $this->getTranslation('description', config('app.locale')),
            'visibility' => $this->visibility,
        ];
    }
}
