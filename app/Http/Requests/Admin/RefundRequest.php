<?php

namespace App\Http\Requests\Admin;

use App\Http\Requests\RequestTrait;
use Illuminate\Foundation\Http\FormRequest;

class RefundRequest extends FormRequest
{
    use RequestTrait;

    /**
     * @var array
     */
    private static $rules = [
        'admin.refunds.store' => [
            'bill_id' => 'required|integer|exists:tenant.bills,id',
            'payment_id' => 'required|integer|exists:tenant.payments,id',
            'amount' => 'required|numeric',
            'status' => 'required|integer|in:0,1,2',
        ],
        'admin.refunds.update' => [
            'bill_id' => 'required|integer|exists:tenant.bills,id',
            'payment_id' => 'required|integer|exists:tenant.payments,id',
            'amount' => 'required|numeric',
            'status' => 'required|integer|in:0,1,2',
        ],
    ];

    public function attributes()
    {
        return [
            'bill_id' => __('validation.refund.bill'),
            'payment_id' => __('validation.refund.payment'),
            'amount' => __('validation.refund.amount'),
            'status' => __('validation.refund.status'),
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
