<?php

namespace App\Http\Requests\Admin;

use App\Http\Requests\RequestTrait;
use Illuminate\Foundation\Http\FormRequest;
use Request;

class BillRequest extends FormRequest
{
    use RequestTrait, EditableAwareRequest;

    /**
     * @var array
     */
    private static $rules = [
//        'admin.orders.store' => [
//            'value' => 'required',
//            'value_type' => 'required',
//            'description' => 'string|nullable',
//        ],
        'admin.bills.update' => [
            'time_wait' => 'time',
            'status' => 'string',
            'paid' => 'boolean',
        ],
        'admin.bills.refund' => [
            'refund_amount_type' => 'required|in:0,1',
            'refund_amount' => 'required|numeric',
        ],
    ];

    public function attributes()
    {
        return [
            'time_wait' => __('validation.bill.time_wait'),
            'status' => __('validation.bill.status'),
            'paid' => __('validation.bill.paid'),
            'refund_amount_type' => __('validation.bill.refund_amount_type'),
            'refund_amount' => __('validation.bill.refund_amount'),
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
