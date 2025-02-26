<?php

namespace App\Http\Resources\Admin;

use App\Http\Resources\ResourceTrait;
use Illuminate\Http\Resources\Json\JsonResource;

class SettingsResource extends JsonResource
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
            case 'value':
                return json_encode($this->resource->value, JSON_UNESCAPED_UNICODE);
                break;
            case 'value_type': return json_encode($this->resource->value_type, JSON_UNESCAPED_UNICODE);
                break;
            case 'value_active': return json_encode($this->resource->value_active, JSON_UNESCAPED_UNICODE);
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
    public function toArray($request)
    {
        $array = [
            'id' => $this->id,
            'key' => $this->key,
            'value' => $this->value,
            'value_type' => $this->value_type,
            'value_active' => $this->value_active,
            'description' => $this->description,
        ];

        return $array;
    }
}
