<?php

namespace App\Http\Requests\Admin;

use App\Http\Requests\RequestTrait;
use Illuminate\Foundation\Http\FormRequest;

class AdditionGroupRequest extends FormRequest
{
    use RequestTrait;

    /**
     * @var array
     */
    private static $rules = [
        'admin.additions_groups.store' => [
            'addition_group_category.*.id' => 'nullable|min:1|exists:tenant.food_categories,id',
            'addition_group_dish.*.id' => 'nullable|min:1|exists:tenant.dishes,id',
            'name.*' => 'nullable|string|min:3|max:100',
            'name.pl' => 'required|string|min:3|max:100',
            'type' => 'boolean',
            'mandatory' => 'boolean',
        ],
        'admin.additions_groups.update' => [
            'addition_group_category.*.id' => 'nullable|min:1|exists:tenant.food_categories,id',
            'addition_group_dish.*.id' => 'nullable|min:1|exists:tenant.dishes,id',
            'name.*' => 'nullable|string|min:3|max:100',
            'name.pl' => 'required|string|min:3|max:100',
            'type' => 'boolean',
            'mandatory' => 'boolean',
        ],
    ];

    public function attributes()
    {
        return [
            'addition_group_category.*.id' => __('validation.addition_group.category'),
            'addition_group_dish.*.id' => __('validation.addition_group.dishes'),
            'name.*' => __('validation.addition_group.name'),
            'type' => __('validation.addition_group.type'),
            'mandatory' => __('validation.addition_group.mandatory'),
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
