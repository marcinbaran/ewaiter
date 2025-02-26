<?php

namespace App\Http\Requests\Admin;

use App\Http\Requests\RequestTrait;
use App\Rules\Dish\PriceRule;
use Illuminate\Foundation\Http\FormRequest;

class DishRequest extends FormRequest
{
    use RequestTrait;

    /**
     * @var array
     */
    private static $rules = [
        'admin.dishes.store' => [
            'name' => 'array|min:1|max:30',
            'name.pl' => 'required|min:3|max:100',
            'name.*' => 'nullable|min:3|max:100',
            'description.*' => 'nullable|string|min:3|max:1000',
            'position' => 'nullable|integer|min:0',
            'price' => [
                'required',
                'numeric',
            ],
            'timeWait' => 'nullable|integer|min:1',
            'tags.*.id' => 'nullable|min:1|exists:tenant.tags,id',
            'additions_groups.*.id' => 'nullable|min:1|exists:tenant.additions_groups,id',
            'category.id' => 'required|integer|exists:tenant.food_categories,id',
            'availability.m' => 'boolean',
            'availability.t' => 'boolean',
            'availability.w' => 'boolean',
            'availability.r' => 'boolean',
            'availability.f' => 'boolean',
            'availability.u' => 'boolean',
            'availability.s' => 'boolean',
            'availability.start_hour' => 'nullable|required_with:availability.end_hour|date_format:H:i|before:availability.end_hour',
            'availability.end_hour' => 'nullable|required_with:availability.start_hour|date_format:H:i|after:availability.start_hour',
            'visibility' => 'boolean',
            'delivery' => 'boolean',
        ],
        'admin.dishes.update' => [
            'name' => 'array|min:1|max:30',
            'name.pl' => 'required|min:3|max:100',
            'name.*' => 'nullable|min:3|max:100',
            'description' => 'nullable|array|min:1|max:50',
            'description.*' => 'nullable|string|min:3|max:1000',
            'position' => 'nullable|integer|min:0',
            'price' => [
                'required',
                'numeric',
            ],
            'timeWait' => 'nullable|integer|min:1',
            'tags.*.id' => 'nullable|min:1|exists:tenant.tags,id',
            'additions_groups.*.id' => 'nullable|min:1|exists:tenant.additions_groups,id',
            'category.id' => 'required|integer|exists:tenant.food_categories,id',
            'availability.m' => 'boolean',
            'availability.t' => 'boolean',
            'availability.w' => 'boolean',
            'availability.r' => 'boolean',
            'availability.f' => 'boolean',
            'availability.u' => 'boolean',
            'availability.s' => 'boolean',
            'availability.start_hour' => 'nullable|required_with:availability.end_hour|date_format:H:i|before:availability.end_hour',
            'availability.end_hour' => 'nullable|required_with:availability.start_hour|date_format:H:i|after:availability.start_hour',
            'visibility' => 'boolean',
            'delivery' => 'boolean',
        ],
    ];

    public function attributes()
    {
        return [
            'name' => __('validation.dish.name'),
            'description' => __('validation.dish.description'),
            'name_locale.*' => __('validation.dish.name'),
            'description_locale_*' => __('validation.dish.description'),
            'position' => __('validation.dish.position'),
            'price' => __('validation.dish.price'),
            'timeWait' => __('validation.dish.timeWait'),
            'tags.*.id' => __('validation.dish.tags'),
            'additions_groups.*.id' => __('validation.dish.additions_groups'),
            'category.id' => __('validation.dish.category'),
            'availability.m' => __('validation.dish.availability.monday'),
            'availability.t' => __('validation.dish.availability.tuesday'),
            'availability.w' => __('validation.dish.availability.wednesday'),
            'availability.r' => __('validation.dish.availability.thursday'),
            'availability.f' => __('validation.dish.availability.friday'),
            'availability.u' => __('validation.dish.availability.saturday'),
            'availability.s' => __('validation.dish.availability.sunday'),
            'availability.start_hour' => __('validation.dish.availability.start_hour'),
            'availability.end_hour' => __('validation.dish.availability.end_hour'),
            'visibility' => __('validation.dish.visibility'),
            'delivery' => __('validation.dish.delivery'),
        ];
    }

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
        $name = $this->route()->getName();
        $rules = self::$rules[$name] ?? [];
        $rules['price'][] = new PriceRule();
        if (empty($rules) || ! in_array($name, ['admin.dishes.store', 'admin.dishes.update'])) {
            return $rules;
        }

        return $rules;
    }
}
