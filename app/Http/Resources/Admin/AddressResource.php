<?php

namespace App\Http\Resources\Admin;

use App\Http\Resources\ResourceTrait;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AddressResource extends JsonResource
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
            'company_name' => $this->company_name,
            'nip' => $this->nip,
            'name' => $this->name,
            'surname' => $this->surname,
            'city' => $this->city,
            'postcode' => $this->postcode,
            'street' => $this->street,
            'building_number' => $this->building_number,
            'house_number' => $this->house_number,
            'floor' => $this->floor,
            'phone' => $this->phone,
            'lat' => $this->lat,
            'lng' => $this->lng,
            'radius' => $this->radius,
            'createdAt' => $this->dateFormat($this->created_at),
            'updatedAt' => $this->dateFormat($this->updated_at),
        ];

        return $array;
    }
}
