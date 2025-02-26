<?php

namespace App\Http\Requests\Admin;

use App\Http\Requests\RequestTrait;
use Illuminate\Foundation\Http\FormRequest;
use Request;

class RestaurantRequest extends FormRequest
{
    use RequestTrait;

    /**
     * @var array
     */
    private static $rules = [
        'admin.restaurants.store' => [
            'visiblity' => 'boolean',
            'name' => 'required|string|min:3|max:100',
            'hostname' => 'required|regex:/^[a-z0-9]+$/u|min:3|max:64',
            'hostname_id' => 'nullable|integer',
            'table_reservation_active' => 'boolean',
            'tag_checkbox.*.id' => 'nullable|min:1|exists:system.restaurant_tags,id',
            'manager_email' => 'required|email|min:3|max:255',
            'address.postcode' => 'required|string|min:6|max:6|regex:/^[0-9]{2}-[0-9]{3}$/u',
            'address.city' => 'required|string|min:3|max:50',
            'address.street' => 'required|string|min:3|max:70',
            'address.building_number' => 'required|string|min:1|max:10',
            'address.house_number' => 'nullable|string|min:1|max:10',
            'address.phone' => 'required|string|min:11|max:11',
            'account_number' => 'nullable|string|min:32|max:32',
            'provision' => 'required|integer|min:0|max:100',
        ],
        'admin.restaurants.update' => [
            'visiblity' => 'boolean',
            'name' => 'required|string|min:3|max:100',
            'hostname' => 'required|regex:/^[a-z0-9]+$/u|min:3|max:64',
            'hostname_id' => 'nullable|integer',
            'table_reservation_active' => 'boolean',
            'tag_checkbox.*.id' => 'nullable|min:1|exists:system.restaurant_tags,id',
            'manager_email' => 'required|email|min:3|max:255',
            'address.postcode' => 'required|string|min:6|max:6|regex:/^[0-9]{2}-[0-9]{3}$/u',
            'address.city' => 'required|string|min:3|max:50',
            'address.street' => 'required|string|min:3|max:70',
            'address.building_number' => 'required|string|min:1|max:10',
            'address.house_number' => 'nullable|string|min:1|max:10',
            'address.phone' => 'required|string|min:11|max:11',
            'account_number' => 'nullable|string|min:32|max:32',
            'provision' => 'required|integer|min:0|max:100',
        ],
    ];

    public function attributes()
    {
        return [
            'visiblity' => __('validation.restaurant.visiblity'),
            'name' => __('validation.restaurant.name'),
            'hostname' => __('validation.restaurant.hostname'),
            'hostname_id' => __('validation.restaurant.hostname'),
            'manager_email' => __('validation.restaurant.manager_email'),
            'table_reservation_active' => __('validation.restaurant.table_reservation_active'),
            'tag_checkbox.*.id' => __('validation.restaurant.tags'),
            'address.postcode' => __('validation.restaurant.address.postcode'),
            'address.city' => __('validation.restaurant.address.city'),
            'address.street' => __('validation.restaurant.address.street'),
            'address.building_number' => __('validation.restaurant.address.building_number'),
            'address.house_number' => __('validation.restaurant.address.house_number'),
            'address.phone' => __('validation.restaurant.address.phone'),
            'account_number' => __('validation.restaurant.account_number'),
            'provision' => __('validation.restaurant.provision'),
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
