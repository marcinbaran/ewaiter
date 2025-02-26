<?php

namespace App\Http\Requests\Admin;

use App\Http\Requests\RequestTrait;
use Illuminate\Foundation\Http\FormRequest;
use Request;

class AddressRequest extends FormRequest
{
    use RequestTrait;

    /**
     * @var array
     */
    private static $rules = [
        'admin.restaurants.store' => [
            'company_name' => 'nullable|string|max:100',
            'nip' => 'nullable|integer|max:10',
            'name' => 'nullable|string|max:100',
            'surname' => 'nullable|string|max:100',
            'city' => 'required|string|max:100',
            'postcode' => 'required|string|max:10',
            'street' => 'nullable|string|max:100',
            'building_number' => 'nullable|string|max:50',
            'house_number' => 'nullable|string|max:50',
            'floor' => 'nullable|string|max:50',
            'phone' => 'nullable|string|max:15',
        ],
        'admin.restaurants.update' => [
            'company_name' => 'nullable|string|max:100',
            'nip' => 'nullable|integer|max:10',
            'name' => 'nullable|string|max:100',
            'surname' => 'nullable|string|max:100',
            'city' => 'required|string|max:100',
            'postcode' => 'required|string|max:10',
            'street' => 'nullable|string|max:100',
            'building_number' => 'nullable|string|max:50',
            'house_number' => 'nullable|string|max:50',
            'floor' => 'nullable|string|max:50',
            'phone' => 'nullable|string|max:15',
        ],
    ];

    public function attributes()
    {
        return [
            'company_name' => __('validation.address.company_name'),
            'nip' => __('validation.address.nip'),
            'name' => __('validation.address.name'),
            'surname' => __('validation.address.surname'),
            'city' => __('validation.address.city'),
            'postcode' => __('validation.address.postcode'),
            'street' => __('validation.address.street'),
            'building_number' => __('validation.address.building_number'),
            'house_number' => __('validation.address.house_number'),
            'floor' => __('validation.address.floor'),
            'phone' => __('validation.address.phone'),
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
