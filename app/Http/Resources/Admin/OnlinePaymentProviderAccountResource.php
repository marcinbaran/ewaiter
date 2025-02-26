<?php

namespace App\Http\Resources\Admin;

use App\Http\Resources\ResourceTrait;
use Illuminate\Http\Resources\Json\JsonResource;

class OnlinePaymentProviderAccountResource extends JsonResource
{
    use ResourceTrait;

    public const LIMIT = 20;

    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'login' => $this->login,
            'password' => $this->password,
            'api_key' => $this->api_key,
            'api_password' => $this->api_password,
            'restaurant_id' => $this->restaurant_id,
        ];
    }
}
