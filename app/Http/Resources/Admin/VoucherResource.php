<?php

namespace App\Http\Resources\Admin;

use App\Http\Resources\ResourceTrait;
use Illuminate\Http\Resources\Json\JsonResource;

class VoucherResource extends JsonResource
{
    use ResourceTrait;

    public const LIMIT = 20;

    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'comment' => $this->comment,
            'code' => $this->code,
            'value' => $this->value,
            'used_by' => $this->used_by,
            'used_at' => $this->used_at,
        ];
    }
}
