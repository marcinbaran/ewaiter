<?php

namespace App\Http\Resources\Admin;

use App\Http\Resources\ResourceTrait;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class WorktimeResource extends JsonResource
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
            'start' => $this->start,
            'end' => $this->end,
            'type' => $this->type,
            'visibility' => $this->visibility,
        ];

        return $array;
    }
}
