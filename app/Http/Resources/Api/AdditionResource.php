<?php

namespace App\Http\Resources\Api;

use App\Http\Resources\ResourceTrait;
use App\Models\Addition;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *     schema="AdditionResource",
 *     type="object",
 *     title="Addition Resource",
 *     description="Representation of an addition resource",
 *     @OA\Property(
 *         property="id",
 *         type="integer",
 *         description="Unique identifier for the addition"
 *     ),
 *     @OA\Property(
 *         property="name",
 *         type="string",
 *         description="Name of the addition"
 *     ),
 *     @OA\Property(
 *         property="price",
 *         type="number",
 *         format="float",
 *         description="Price of the addition"
 *     ),
 *     @OA\Property(
 *         property="type",
 *         type="integer",
 *         description="Type of the addition"
 *     ),
 *     @OA\Property(
 *         property="quantity",
 *         type="integer",
 *         description="Quantity of the addition"
 *     )
 * )
 */
class AdditionResource extends JsonResource
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
        $addition = Addition::find($this->id);

        if ($addition) {
            $name = $addition->name;
        } elseif ($this->name) {
            $name = $this->name;
        } else {
            $name = 'Dodatek został usunięty';
        }

        $array = [
            'id' => $this->id,
            'name' => $name,
            'price' => number_format($this->price, 2, '.', ''),
            'type' => (int) $this->type,
            'quantity' => (int) $this->quantity,
            'dish_id' => $this->dish_id,
        ];
//        if ($this->isWithDish($request)) {
//            $array['dish'] = new DishResource($th
        //is->dish);
//        }

        return $array;
    }

    /**
     * @param Request $request
     *
     * @return bool
     */
    protected function isWithDish(Request $request): bool
    {
        return $this->isAdditionsRoute($request);
    }
}
