<?php

namespace App\Http\Resources\Api;

use App\Http\Resources\ResourceTrait;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
/**
 * @OA\Schema(
 *     schema="RestaurantAddress",
 *     type="object",
 *     title="Restaurant Address",
 *     description="Details of the restaurant's address"
 * )
 */
class RestaurantAddressResource extends JsonResource
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
    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     *
     * @return array
     *
     * @OA\Property(
     *     property="id",
     *     type="integer",
     *     description="The unique identifier of the address"
     * )
     * @OA\Property(
     *     property="city",
     *     type="string",
     *     description="The city of the restaurant's location"
     * )
     * @OA\Property(
     *     property="postcode",
     *     type="string",
     *     description="The postal code of the restaurant's location"
     * )
     * @OA\Property(
     *     property="street",
     *     type="string",
     *     description="The street name of the restaurant's location"
     * )
     * @OA\Property(
     *     property="building_number",
     *     type="string",
     *     description="The building number of the restaurant's location"
     * )
     * @OA\Property(
     *     property="house_number",
     *     type="string",
     *     description="The house number of the restaurant's location"
     * )
     */
    public function toArray($request): array
    {
        $array = [
            'id' => $this->id,
            'city' => $this->city,
            'postcode' => $this->postcode,
            'street' => $this->street,
            'building_number' => $this->building_number,
            'house_number' => $this->house_number,
        ];

        return $array;
    }
}
