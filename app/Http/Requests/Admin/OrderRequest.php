<?php

namespace App\Http\Requests\Admin;

use App\Http\Requests\RequestTrait;
use Illuminate\Foundation\Http\FormRequest;
use Request;

class OrderRequest extends FormRequest
{
    use RequestTrait;

    /**
     * @var array
     */
    private static $rules = [
//        'admin.orders.store' => [
//            'value' => 'required',
//            'value_type' => 'required',
//            'description' => 'string|nullable',
//        ],
        'admin.orders.update' => [
            'status' => 'required|integer',
        ],
        'admin.orders.status_edit' => [
            'status' => 'required|integer',
        ],
        'admin.orders.modal_table' => [
            'bill_id' => 'required|integer',
        ],
    ];

    public function attributes()
    {
        return [
            'status' => __('validation.order.status'),
            'bill_id' => __('validation.order.bill'),
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
