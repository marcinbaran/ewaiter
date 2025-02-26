<?php

namespace App\Http\Resources\Api;

use App\Http\Resources\ResourceTrait;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
/**
 * @OA\Schema(
 *     schema="TableResource",
 *     type="object",
 *     title="Table Resource",
 *     description="Table resource representation",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="name", type="string", example="Table 1"),
 *     @OA\Property(property="peopleNumber", type="integer", example=4),
 *     @OA\Property(property="description", type="string", example="Near the window"),
 *     @OA\Property(property="active", type="boolean", example=true),
 *     @OA\Property(
 *         property="orders",
 *         type="array",
 *         @OA\Items(ref="#/components/schemas/OrderResource")
 *     )
 * )
 */
class TableResource extends JsonResource
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
            'name' => __($this->name),
            'peopleNumber' => $this->people_number,
            'description' => $this->description,
            'active' => $this->active,
        ];
        if ($this->isWithOrders($request)) {
            $array['orders'] = OrderResource::collection($this->orders);
        }

        return $array;
    }

    /**
     * @param Request $request
     *
     * @return bool
     */
    protected function isWithOrders(Request $request): bool
    {
        return $this->isTablesRoute($request) && (! $this->isRootRoute($request) || $request->withOrders);
    }

    /**
     * @param Request $request
     *
     * @return bool
     */
    protected function isRootRoute(Request $request): bool
    {
        return 'tables.index' == $request->route()->getName();
    }
}
