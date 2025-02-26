<?php

namespace App\Http\Resources\Admin;

use App\Http\Resources\ResourceTrait;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TableResource extends JsonResource
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
            'name' => __($this->name),
            'peopleNumber' => $this->people_number,
            'description' => $this->description,
            'active' => $this->active,
        ];

        return $array;
    }
}
