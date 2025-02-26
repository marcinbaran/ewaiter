<?php

namespace App\Http\Requests\Admin;

use App\Http\Requests\RequestTrait;
use Illuminate\Foundation\Http\FormRequest;

class FoodCategoryRequest extends FormRequest
{
    use RequestTrait;

    /**
     * @var array
     */
    private static $rules = [
        'admin.categories.store' => [
            'name' => 'required|array|min:1|max:50',
            'name.*' => 'nullable|string|min:3|max:100',
            'name.pl' => 'required|string|min:3|max:100',
            'description' => 'nullable|array|min:1|max:50',
            'description.*' => 'nullable|string|min:3|max:1000',
            'parent.id' => 'nullable',
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
        ],
        'admin.categories.update' => [
            'name' => 'required|array|min:1|max:50',
            'name.*' => 'nullable|string|min:3|max:100',
            'name.pl' => 'required|string|min:3|max:100',
            'description' => 'nullable|array|min:1|max:50',
            'description.*' => 'nullable|string|min:3|max:1000',
            'parent.id' => 'nullable',
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
        ],
    ];

    public function attributes()
    {
        return [
            'name' => __('validation.food_category.name'),
            'description' => __('validation.food_category.description'),
            'parent.id' => __('validation.food_category.parent'),
            'position' => __('validation.food_category.position'),
            'availability.m' => __('validation.food_category.availability.monday'),
            'availability.t' => __('validation.food_category.availability.tuesday'),
            'availability.w' => __('validation.food_category.availability.wednesday'),
            'availability.r' => __('validation.food_category.availability.thursday'),
            'availability.f' => __('validation.food_category.availability.friday'),
            'availability.u' => __('validation.food_category.availability.saturday'),
            'availability.s' => __('validation.food_category.availability.sunday'),
            'availability.start_hour' => __('validation.food_category.availability.start_hour'),
            'availability.end_hour' => __('validation.food_category.availability.end_hour'),
            'visibility' => __('validation.food_category.visibility'),
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
        return self::$rules[$this->route()->getName()] ?? [];
    }
}
