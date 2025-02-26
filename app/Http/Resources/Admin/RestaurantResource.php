<?php

namespace App\Http\Resources\Admin;

use App\Http\Resources\ResourceTrait;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RestaurantResource extends JsonResource
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
            case 'photo':
                return $val ? new ResourceResource($val) : null;
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
        $array = [
            'id' => $this->id,
            'name' => $this->name,
            'subname' => $this->subname,
            'hostname' => $this->hostname,
            'hostname_id' => $this->hostname_id,
            'description' => $this->description,
            'photo' => $this->photo,
            'provision' => $this->provision,
            'provisionLogged' => $this->provision_logged,
            'provisionUnlogged' => $this->provision_unlogged,
            'accountNumber' => $this->account_number,
            'address' => new AddressResource($this->address),
            'createdAt' => $this->dateFormat($this->created_at),
            'updatedAt' => $this->dateFormat($this->updated_at),
        ];

        return $array;
    }

    /**
     * @return string
     */
    public function isVisibility(): string
    {
        return $this->visibility ? 'Yes' : 'No';
    }
}
