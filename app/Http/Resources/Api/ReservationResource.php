<?php

namespace App\Http\Resources\Api;

use App\Http\Resources\ResourceTrait;
use App\Models\Restaurant;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
/**
 * @OA\Schema(
 *     schema="ReservationResource",
 *     type="object",
 *     title="Reservation Resource",
 *     description="Resource representing a reservation"
 * )
 */
class ReservationResource extends JsonResource
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

    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     *
     * @OA\Property(
     *     property="id",
     *     type="integer",
     *     description="The ID of the reservation"
     * ),
     * @OA\Property(
     *     property="peopleNumber",
     *     type="integer",
     *     description="Number of people for the reservation"
     * ),
     * @OA\Property(
     *     property="start",
     *     type="string",
     *     format="date-time",
     *     description="The start time of the reservation"
     * ),
     * @OA\Property(
     *     property="tableId",
     *     type="integer",
     *     description="The ID of the table"
     * ),
     * @OA\Property(
     *     property="table_name",
     *     type="string",
     *     description="The name of the table"
     * ),
     * @OA\Property(
     *     property="table",
     *     ref="#/components/schemas/TableResource",
     *     description="The table associated with the reservation"
     * ),
     * @OA\Property(
     *     property="userId",
     *     type="integer",
     *     description="The ID of the user who made the reservation"
     * ),
     * @OA\Property(
     *     property="active",
     *     type="boolean",
     *     description="Indicates if the reservation is active"
     * ),
     * @OA\Property(
     *     property="closed",
     *     type="boolean",
     *     description="Indicates if the reservation is closed"
     * ),
     * @OA\Property(
     *     property="name",
     *     type="string",
     *     description="Name associated with the reservation"
     * ),
     * @OA\Property(
     *     property="description",
     *     type="string",
     *     description="Description of the reservation"
     * ),
     * @OA\Property(
     *     property="phone",
     *     type="string",
     *     description="Phone number associated with the reservation"
     * ),
     * @OA\Property(
     *     property="restaurant_id",
     *     type="integer",
     *     description="The ID of the restaurant"
     * ),
     * @OA\Property(
     *     property="restaurant_name",
     *     type="string",
     *     description="The name of the restaurant"
     * ),
     * @OA\Property(
     *     property="status",
     *     type="string",
     *     description="The status of the reservation"
     * )
     */

    public function toArray($request): array
    {
        $currentRestaurant = Restaurant::getCurrentRestaurant();

        return [
            'id' => $this->id,
            'peopleNumber' => $this->people_number,
            'start' => $this->start,
            //'end' => $this->end,
            'tableId' => $this->table_id,
            'table_name' => $this->active ? $this->table?->name : null,
            'table' => $this->active ? new TableResource($this->table) : null,
            'userId' => $this->user_id,
            //'user' => UserSystemResource::collection($this->user),
            //'kid' => $this->kid,
            'active' => $this->active,
            'closed' => $this->closed,
            'name' => $this->name,
            'description' => $this->description,
            'phone' => $this->phone,
            'restaurant_id' => ($currentRestaurant) ? $currentRestaurant->id : $this->restaurant_id,
            'restaurant_name' => ($currentRestaurant) ? $currentRestaurant->name : $this->restaurant_name,
            'status' => $this->status,
        ];
    }
}
