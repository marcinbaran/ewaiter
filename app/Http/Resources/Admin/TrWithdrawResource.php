<?php

namespace App\Http\Resources\Admin;

use App\Http\Resources\ResourceTrait;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TrWithdrawResource extends JsonResource
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
            'tag' => $this->tag,
            'name' => gtrans('tags.'.$this->name),
            'icon' => $this->icon,
            'description' => gtrans('tags.'.$this->description),
            'visibility' => $this->visibility,
        ];
    }
}
