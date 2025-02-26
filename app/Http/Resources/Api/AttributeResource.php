<?php

namespace App\Http\Resources\Api;

use App\Http\Requests\Api\AttributeRequest;
use App\Http\Resources\ResourceTrait;
use Illuminate\Http\Request;
/**
 * @OA\Schema(
 *     schema="AttributeResource",
 *     type="object",
 *     title="Attribute Resource",
 *     description="Representation of an attribute resource",
 *     @OA\Property(
 *         property="id",
 *         type="integer",
 *         description="Unique identifier for the attribute"
 *     ),
 *     @OA\Property(
 *         property="key",
 *         type="string",
 *         description="Key identifier for the attribute"
 *     ),
 *     @OA\Property(
 *         property="name",
 *         type="string",
 *         description="Translated name of the attribute"
 *     ),
 *     @OA\Property(
 *         property="description",
 *         type="string",
 *         description="Translated description of the attribute"
 *     ),
 *     @OA\Property(
 *         property="icon",
 *         type="string",
 *         nullable=true,
 *         description="Icon associated with the attribute"
 *     ),
 *     @OA\Property(
 *         property="attribute_group_id",
 *         type="integer",
 *         description="Identifier of the group this attribute belongs to"
 *     ),
 *     @OA\Property(
 *         property="attribute_group",
 *         type="object",
 *         nullable=true,
 *         description="Attribute group resource",
 *         @OA\Schema(ref="#/components/schemas/AttributeGroupResource")
 *     )
 * )
 */
class AttributeResource extends ApiResource
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
            'icon' => $this->icon,
            'attribute_group_id' => $this->attribute_group_id,
        ];

        // TODO: decouple this action from request.
        if ($request->get(AttributeRequest::WITH_ATTRIBUTE_GROUP_KEY)) {
            $array['attribute_group'] = new AttributeGroupResource($this->attributeGroup);
        }

        return $array;
    }
}
