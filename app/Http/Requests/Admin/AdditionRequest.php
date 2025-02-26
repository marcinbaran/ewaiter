<?php

namespace App\Http\Requests\Admin;

use App\Http\Requests\RequestTrait;
use Illuminate\Foundation\Http\FormRequest;
/**
 * @OA\Schema(
 *     schema="AdditionRequest",
 *     type="object",
 *     title="Addition Request",
 *     description="Request body data for creating or updating an addition",
 *     required={"name"},
 *     @OA\Property(
 *         property="addition_addition_group",
 *         type="array",
 *         @OA\Items(
 *             @OA\Property(
 *                 property="id",
 *                 type="integer",
 *                 nullable=true,
 *                 description="The ID of the addition group"
 *             )
 *         ),
 *         description="The addition groups associated with the addition"
 *     ),
 *     @OA\Property(
 *         property="name",
 *         type="object",
 *         description="Name of the addition in multiple languages",
 *         @OA\Property(
 *             property="pl",
 *             type="string",
 *             description="Name of the addition in Polish",
 *             example="Dodatkowy ser"
 *         ),
 *         @OA\Property(
 *             property="en",
 *             type="string",
 *             description="Name of the addition in English",
 *             example="Extra Cheese"
 *         )
 *     ),
 *     @OA\Property(
 *         property="price",
 *         type="number",
 *         format="float",
 *         description="Price of the addition",
 *         example=2.50
 *     )
 * )
 */
class AdditionRequest extends FormRequest
{
    use RequestTrait;

    /**
     * @var array
     */
    private static $rules = [
        'admin.additions.store' => [
            'addition_addition_group.*.id' => 'nullable|min:1|exists:tenant.additions_groups,id',
            'name' => 'required|array|min:1|max:50',
            'name.*' => 'nullable|string|min:3|max:50',
            'name.pl' => 'required|string|min:3|max:50',
            'name_locale.*' => 'nullable|string|min:3|max:50',
            'price' => 'nullable|numeric|min:0|max:9999',
        ],
        'admin.additions.update' => [
            'addition_addition_group.*.id' => 'nullable|min:1|exists:tenant.additions_groups,id',
            'name' => 'required|array|min:1|max:50',
            'name.*' => 'nullable|string|min:3|max:50',
            'name.pl' => 'required|string|min:3|max:50',
            'name_locale.*' => 'nullable|string|min:3|max:50',
            'price' => 'nullable|numeric|min:0|max:9999',
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
        return self::$rules[$this->route()->getName()] ?? [];
    }

    public function attributes()
    {
        return [
            'addition_addition_group.*.id' => __('validation.addition.groups'),
            'name' => __('validation.addition.name'),
            'name.*' => __('validation.addition.name'),
            'name_locale.*' => __('validation.addition.name'),
            'price' => __('validation.addition.price'),
        ];
    }
}
