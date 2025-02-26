<?php

namespace App\Http\Requests\Admin;

use App\Http\Requests\RequestTrait;
use App\Rules\DeliveryRange\OutOfRangeDeliveryExists;
use App\Rules\DeliveryRange\PolygonInsideOther;
use App\Rules\DeliveryRange\RestaurantOutsideTheZone;
use Illuminate\Foundation\Http\FormRequest;

class DeliveryRangeRequest extends FormRequest
{
    use RequestTrait;

    /**
     * @var array
     */
    private static $rules = [
        'admin.delivery_ranges.store' => [
            'name' => 'nullable|string|min:3|max:50',
            'out_of_range' => 'boolean',
            'range_polygon' => 'required|string',
            'min_value' => 'nullable|numeric|min:0',
            'free_from' => 'nullable|numeric|min:0',
            'cost' => 'required|numeric|min:0',
        ],
        'admin.delivery_ranges.update' => [
            'name' => 'nullable|string|min:3|max:50',
            'out_of_range' => 'boolean',
            'range_polygon' => 'required|string',
            'min_value' => 'nullable|numeric|min:0',
            'free_from' => 'nullable|numeric|min:0',
            'cost' => 'required|numeric|min:0',
        ],
    ];

    public function attributes()
    {
        return [
            'name' => __('validation.delivery_range.name'),
            'out_of_range' => __('validation.delivery_range.out_of_range'),
            'range_polygon' => __('validation.delivery_range.range_polygon'),
            'min_value' => __('validation.delivery_range.min_value'),
            'free_from' => __('validation.delivery_range.free_from'),
            'cost' => __('validation.delivery_range.cost'),
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
        if ($this->route()->getName() === 'admin.delivery_ranges.store') {
            $rules['out_of_range'] = ['boolean', new OutOfRangeDeliveryExists()];
            $rules['range_polygon'] = ['required', new RestaurantOutsideTheZone(), new PolygonInsideOther()];
        }


        return $rules;
    }
}
