<?php

namespace App\Http\Resources\Admin;

use App\Http\Resources\ResourceTrait;
use Illuminate\Http\Resources\Json\JsonResource;

class PlayerIdResource extends JsonResource
{
    use ResourceTrait;

    /**
     * @var int Default limit items per page
     */
    public const LIMIT = 20;

    public function __get($key)
    {
        $val = parent::__get($key);

        return 'user' == $key ? new UserResource($val) : $val;
    }

    public function toArray($request)
    {
        $array = [
            'id' => $this->id,
            'user' => $this->user,
            'playerId' => $this->player_id,
            'deviceInfo' => __($this->device_info),
        ];

        return $array;
    }
}
