<?php

namespace App\Http\Resources\Api;

use App\Http\Resources\ResourceTrait;
use App\Models\Dish;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
/**
 * @OA\Schema(
 *     schema="BundleResource",
 *     type="object",
 *     title="Bundle Resource",
 *     description="Bundle resource representation",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="name", type="string", example="Summer Special Bundle"),
 *     @OA\Property(property="description", type="string", example="A special bundle for the summer season"),
 *     @OA\Property(property="photoUrl", type="string", example="https://example.com/image.jpg"),
 *     @OA\Property(property="price", type="number", format="float", example=29.99),
 *     @OA\Property(property="delivery", type="boolean", example=true),
 *     @OA\Property(property="createdAt", type="string", format="date-time", example="2021-08-15T15:52:01+00:00"),
 *     @OA\Property(
 *         property="dishes",
 *         type="array",
 *         @OA\Items(ref="#/components/schemas/DishResource")
 *     )
 * )
 */
class BundleResource extends ApiResource
{
    use ResourceTrait;

    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'photoUrl' => $this->photo_url,
            'price' => $this->value,
            'delivery' => $this->delivery,
            'createdAt' => $this->created_at,
            'dishes' => $this->customDishes ?? $this->getDishes(),
        ];
    }

    public function getDishes(): array
    {
        if ($this->customDishes !== null) {
            return $this->customDishes;
        }

        $promotionDishes = $this->promotion_dishes;
        $dishIds = $promotionDishes->pluck('dish_id')->toArray();
        $dishes = Dish::whereIn('id', $dishIds)->whereNull('deleted_at')->get();

        return DishResource::collection($dishes)->toArray(request());
    }

    public function setDishes(array $dishes): self
    {
        $this->customDishes = $dishes;
        return $this;
    }

}
