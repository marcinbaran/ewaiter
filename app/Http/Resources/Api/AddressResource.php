<?php

namespace App\Http\Resources\Api;

use App\Http\Resources\ResourceTrait;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
/**
 * @OA\Schema(
 *     schema="AddressResource",
 *     type="object",
 *     @OA\Property(property="id", type="integer", description="ID", example=1),
 *     @OA\Property(property="company_name", type="string", description="Company name", example="Example Inc."),
 *     @OA\Property(property="nip", type="string", description="NIP (tax identification number)", example="1234567890"),
 *     @OA\Property(property="name", type="string", description="First name", example="John"),
 *     @OA\Property(property="surname", type="string", description="Last name", example="Doe"),
 *     @OA\Property(property="city", type="string", description="City", example="Warsaw"),
 *     @OA\Property(property="postcode", type="string", description="Postal code", example="00-001"),
 *     @OA\Property(property="street", type="string", description="Street", example="Main Street"),
 *     @OA\Property(property="building_number", type="string", description="Building number", example="10"),
 *     @OA\Property(property="house_number", type="string", description="House number", example="5A"),
 *     @OA\Property(property="floor", type="string", description="Floor", example="3"),
 *     @OA\Property(property="phone", type="string", description="Phone number", example="+48123456789"),
 *     @OA\Property(property="is_default", type="boolean", description="Is default address", example=true),
 *     @OA\Property(property="lat", type="number", format="float", description="Latitude", example=52.2297),
 *     @OA\Property(property="lng", type="number", format="float", description="Longitude", example=21.0122),
 *     @OA\Property(property="radius", type="number", format="float", description="Radius", example=100.0)
 * )
 */
class AddressResource extends JsonResource
{
    use ResourceTrait;

    /**
     * @var int Default limit items per page
     */
    public const LIMIT = 20;

    protected $casts = [
        'is_default' => 'bool',
    ];

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
            'is_default' => $this->is_default == 1 ? true : false,
            'lat' => $this->lat,
            'lng' => $this->lng,
            'radius' => $this->radius,
            'createdAt' => $this->dateFormat($this->created_at),
            'updatedAt' => $this->dateFormat($this->updated_at),
        ];

        return $array;
    }
}
