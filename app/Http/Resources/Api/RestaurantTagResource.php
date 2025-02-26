<?php

namespace App\Http\Resources\Api;

use App\Http\Resources\ResourceTrait;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
/**
 * @OA\Schema(
 *     schema="RestaurantTagResource",
 *     type="object",
 *     description="Restaurant tag resource schema",
 *     @OA\Property(
 *         property="key",
 *         type="string",
 *         description="The unique key for the tag"
 *     ),
 *     @OA\Property(
 *         property="value",
 *         type="string",
 *         description="The localized value of the tag"
 *     )
 * )
 */
class RestaurantTagResource extends JsonResource
{
    use ResourceTrait;
    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array
     */
    public function toArray(Request $request)
    {
        $locale = $request->get('locale') ?? config('app.fallback_locale');
        $array = [
            'key' => $this->key,
            'value' => $this->getTranslationFromValue($locale),
        ];

        return $array;
    }
    /**
     * Get the translated value for the specified locale.
     *
     * @param string $locale
     * @return string
     */
    public function getTranslationFromValue($locale)
    {
        $value = $this->value;
        if (is_array($value)) {
            return $value[$locale] ?? $value[config('app.fallback_locale')] ?? '';
        }

        return $value;
    }
}
