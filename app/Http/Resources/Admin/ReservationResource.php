<?php

namespace App\Http\Resources\Admin;

use App\Http\Resources\ResourceTrait;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ReservationResource extends JsonResource
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
            'people_number' => $this->people_number,
            'start' => $this->start,
            //'end' => $this->end,
            'table_id' => $this->table_id,
            //'table' => TableResource::collection($this->table),
            'user_id' => $this->user_id,
            //'user' => UserSystemResource::collection($this->user),
            //'kid' => $this->kid,
            'active' => $this->active,
            'closed' => $this->closed,
            'name' => $this->name,
            'description' => $this->description,
            'phone' => $this->phone,
        ];
    }
}
