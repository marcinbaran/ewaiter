<?php

namespace App\Http\Resources\Api;

use App\Http\Resources\ResourceTrait;
use App\Models\Attribute;
use Illuminate\Http\Request;
/**
 * @OA\Schema(
 *     schema="AttributeGroupResource",
 *     type="object",
 *     title="Attribute Group Resource",
 *     description="Resource representing an attribute group",
 *     @OA\Property(property="id", type="integer", example=1, description="The ID of the attribute group"),
 *     @OA\Property(property="key", type="string", example="color", description="The key identifier for the attribute group"),
 *     @OA\Property(property="name", type="string", example="Color", description="The name of the attribute group"),
 *     @OA\Property(property="description", type="string", example="Various colors available", description="The description of the attribute group"),
 *     @OA\Property(property="input_type", type="string", example="select", description="The type of input for the attribute group"),
 *     @OA\Property(property="is_primary", type="boolean", example=true, description="Indicates if the attribute group is primary"),
 *     @OA\Property(
 *         property="attributes",
 *         type="array",
 *         @OA\Items(ref="#/components/schemas/AttributeResource"),
 *         description="List of attributes in the group"
 *     )
 * )
 */
class AttributeGroupResource extends ApiResource
{
    use ResourceTrait;

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
            'key' => $this->key,
            'name' => $this->getTranslation('name', config('app.locale')),
            'description' => $this->getTranslation('description', config('app.locale')),
            'input_type' => $this->input_type,
            'is_primary' => $this->is_primary,
        ];

        // TODO: decouple this action from request.
//        if ($request->get(AttributeGroupRequest::WITH_ATTRIBUTES_KEY)) {

        $array['attributes'] = AttributeResource::collection(
            Attribute::query()
                ->where('attribute_group_id', $this->id)
                ->active()
                ->get()
        );
//        }

        return $array;
    }
}
