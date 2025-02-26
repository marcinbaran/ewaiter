<?php

namespace App\Http\Resources\Api;

use App\Http\Resources\ResourceTrait;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
/**
 * @OA\Schema(
 *     schema="LabelResource",
 *     type="object",
 *     title="Label Resource",
 *     description="Resource representing a label with a name and an icon",
 *     @OA\Property(property="id", type="integer", example=1, description="The ID of the label"),
 *     @OA\Property(property="name", type="string", example="New Arrival", description="The name of the label in the requested locale"),
 *     @OA\Property(property="icon", type="string", example="star", description="The icon associated with the label")
 * )
 */
class LabelResource extends JsonResource
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
        $locale = in_array($request->get('locale'), config('app.available_locales')) ? $request->get('locale') : config('app.fallback_locale');

        $array = [
            'id' => $this->id,
            'name' => $this->getTranslation('name', $locale),
            'icon' => $this->icon,
        ];

        return $array;
    }
}
