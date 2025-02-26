<?php

namespace App\Http\Resources\Api;

use App\Http\Resources\ResourceTrait;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *     schema="AdditionGroupResource",
 *     type="object",
 *     title="Addition Group Resource",
 *     description="Resource representing an addition group",
 *     @OA\Property(property="id", type="integer", example=1, description="The ID of the addition group"),
 *     @OA\Property(property="name", type="string", example="Extra Cheese", description="The name of the addition group"),
 *     @OA\Property(property="description", type="string", example="Group of cheese additions", description="The description of the addition group"),
 *     @OA\Property(property="type", type="string", example="checkbox", description="The type of selection for the addition group"),
 *     @OA\Property(property="mandatory", type="boolean", example=true, description="Indicates if the group is mandatory"),
 *     @OA\Property(property="typeName", type="string", example="Multiple Choice", description="The human-readable name of the type"),
 *     @OA\Property(
 *         property="additions",
 *         type="array",
 *         @OA\Items(ref="#/components/schemas/AdditionResource"),
 *         description="List of additions in the group"
 *     )
 * )
 */
class AdditionGroupResource extends JsonResource
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
            'name' => $this->getTranslation('name', app()->currentLocale()),
            'description' => gtrans('additions_groups.'.$this->description),
            'type' => $this->type,
            'mandatory' => $this->mandatory,
            'typeName' => $this->getTypeName(),
        ];

        $array['additions'] = [];
        foreach ($this->additions_additions_groups as $addition_addition_group) {
            $array['additions'][] = new AdditionResource($addition_addition_group->addition);
        }

        return $array;
    }
}
