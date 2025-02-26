<?php

namespace App\Http\Requests\Admin;

use App\Http\Requests\RequestTrait;
use Illuminate\Foundation\Http\FormRequest;

class RestaurantTagRequest extends FormRequest
{
    use RequestTrait;

    /**
     * @var array
     */
    private static $rules = [
        'admin.restaurant_tags.store' => [
            'name' => 'required|string|min:3|max:100',
            'key' => 'required|string|min:3|max:35|unique:restaurant_tags,key',
            'name_locale.*' => 'nullable|string|min:3|max:100',
        ],
        'admin.restaurant_tags.update' => [
            'name' => 'required|string|min:3|max:100',
            'key' => 'required|string|min:3|max:35|unique:restaurant_tags,key',
            'name_locale.*' => 'nullable|string|min:3|max:100',
        ],
    ];

    public function attributes()
    {
        return [
            'name' => __('validation.restaurant_tag.name'),
            'key' => __('validation.restaurant_tag.key'),
            'name_locale.*' => __('validation.restaurant_tag.name'),
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
        $rules = self::$rules[$this->route()->getName()] ?? [];
        if ($this->route()->getName() == 'admin.restaurant_tags.update') {
            $rules['key'] .= ','.request()->route()->parameter('restaurant_tag')->id;
        }

        return $rules;
    }
}
