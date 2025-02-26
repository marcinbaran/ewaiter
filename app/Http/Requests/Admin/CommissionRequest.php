<?php

namespace App\Http\Requests\Admin;

use App\Enum\Commission\CommissionStatus;
use App\Http\Requests\RequestTrait;
use Illuminate\Foundation\Http\FormRequest;

class CommissionRequest extends FormRequest
{
    use RequestTrait;

    /**
     * @var array
     */
    private static $rules = [
        'admin.commission.store' => [
            'restaurant_id' => [
                'required',
                'integer',
                'min:1',
            ],
            'restaurant_name' => [
                'required',
                'string',
                'min:3',
                'max:100',
            ],
            'bill_id' => [
                'required',
                'integer',
                'min:1',
            ],
            'bill_price' => [
                'required',
                'numeric',
                'min:0',
            ],
            'commission' => [
                'required',
                'numeric',
                'min:0',
            ],
            'status' => [
                'required',
                'string',
            ],
            'comment' => [
                'string',
                'max:255',
            ],
        ],
        'admin.commission.update' => [
            'restaurant_id' => [
                'required',
                'integer',
                'min:1',
            ],
            'restaurant_name' => [
                'required',
                'string',
                'min:3',
                'max:100',
            ],
            'bill_id' => [
                'required',
                'integer',
                'min:1',
            ],
            'bill_price' => [
                'required',
                'numeric',
                'min:0',
            ],
            'commission' => [
                'required',
                'numeric',
                'min:0',
            ],
            'status' => [
                'required',
                'string',
            ],
            'comment' => [
                'string',
                'max:255',
            ],
        ],
    ];

    public function attributes()
    {
        return [
            'restaurant_id' => __('labels.restaurant_id'),
            'restaurant_name' => __('labels.restaurant_name'),
            'bill_id' => __('labels.bill_id'),
            'bill_price' => __('labels.bill_price'),
            'commission' => __('labels.commission'),
            'status' => __('labels.status'),
            'comment' => __('labels.comment'),
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

        if (! empty($rules)) {
            $statuses = CommissionStatus::getValuesForRequestRule();
            $rules['status'][] = 'in:'.$statuses;
        }

        return $rules;
    }
}
