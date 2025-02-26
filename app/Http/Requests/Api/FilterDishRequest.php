<?php

namespace App\Http\Requests\Api;

use App\Http\Requests\RequestTrait;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
/**
 * @OA\Schema(
 *     schema="FilterDishRequest_POST",
 *     type="object",
 *     @OA\Property(property="itemsPerPage", type="integer", example=20, description="Number of items per page"),
 *     @OA\Property(property="page", type="integer", example=1, description="Page number"),
 *     @OA\Property(
 *         property="attribute_filters",
 *         type="array",
 *         @OA\Items(
 *             type="object",
 *             @OA\Property(property="id", type="integer", description="Attribute filter ID"),
 *             @OA\Property(property="key", type="string", description="Attribute filter key"),
 *             @OA\Property(property="name", type="string", description="Attribute filter name"),
 *             @OA\Property(property="description", type="string", description="Attribute filter description"),
 *             @OA\Property(property="input_type", type="string", description="Input type"),
 *             @OA\Property(property="is_primary", type="boolean", description="Is primary filter"),
 *             @OA\Property(
 *                 property="attributes",
 *                 type="array",
 *                 @OA\Items(
 *                     type="object",
 *                     @OA\Property(property="id", type="integer", description="Attribute ID"),
 *                     @OA\Property(property="key", type="string", description="Attribute key"),
 *                     @OA\Property(property="name", type="string", description="Attribute name"),
 *                     @OA\Property(property="description", type="string", description="Attribute description"),
 *                     @OA\Property(property="icon", type="string", description="Attribute icon"),
 *                     @OA\Property(property="attribute_group_id", type="integer", description="Attribute group ID"),
 *                 )
 *             )
 *         )
 *     ),
 *     @OA\Property(
 *         property="price_range",
 *         type="object",
 *         @OA\Property(property="min", type="number", format="float", description="Minimum price"),
 *         @OA\Property(property="max", type="number", format="float", description="Maximum price"),
 *     )
 * )
 */
class FilterDishRequest extends FormRequest
{
    use RequestTrait;

    /**
     * @var array
     */
    private static $rules = [
        self::METHOD_POST => [
            'itemsPerPage' => 'integer|min:1|max:50',
            'page' => 'integer|min:1',
            'attribute_filters.*.id' => 'integer|min:1',
            'attribute_filters.*.key' => 'string',
            'attribute_filters.*.name' => 'string',
            'attribute_filters.*.description' => 'string',
            'attribute_filters.*.input_type' => 'string',
            'attribute_filters.*.is_primary' => 'boolean',
            'attribute_filters.*.attributes.*.id' => 'integer|min:1',
            'attribute_filters.*.attributes.*.key' => 'string',
            'attribute_filters.*.attributes.*.name' => 'string',
            'attribute_filters.*.attributes.*.description' => 'string',
            'attribute_filters.*.attributes.*.icon' => 'string',
            'attribute_filters.*.attributes.*.attribute_group_id' => 'integer|min:1',
            'price_range.min' => 'numeric|nullable',
            'price_range.max' => 'numeric|nullable',
        ],
    ];

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return self::$rules[$this->getMethod()] ?? [];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function ($validator) {
            $data = $this->all();

            if (isset($data['price_range']['min']) && isset($data['price_range']['max'])) {
                $min = $data['price_range']['min'];
                $max = $data['price_range']['max'];

                if ($min !== null && $max !== null) {
                    if ($min >= $max) {
                        $validator->errors()->add('price_range.min', 'The minimum price must be less than the maximum price.');
                        $validator->errors()->add('price_range.max', 'The maximum price must be greater than the minimum price.');
                    }
                }
            }
        });
    }
}
