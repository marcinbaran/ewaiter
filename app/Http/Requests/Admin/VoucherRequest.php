<?php

namespace App\Http\Requests\Admin;

use App\Http\Requests\RequestTrait;
use Illuminate\Foundation\Http\FormRequest;

class VoucherRequest extends FormRequest
{
    use RequestTrait;

    private static $rules = [
        'admin.vouchers.store' => [
            'adding_type' => 'required|integer|in:0,1',
            'quantity' => 'nullable|required_if:adding_type,1|integer|min:2',
            'comment' => 'required|string|min:3|max:100',
            'value' => 'required|numeric|min:1|max:500',
        ],
        'admin.vouchers.update' => [
            'comment' => 'required|string|min:3|max:100',
            'value' => 'required|numeric|min:1|max:500',
        ],
    ];

    public function attributes()
    {
        return [
            'adding_type' => __('voucher.form.adding_type'),
            'quantity' => __('voucher.form.quantity'),
            'comment' => __('voucher.form.comment'),
            'value' => __('voucher.form.value'),
        ];
    }

    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return self::$rules[$this->route()->getName()] ?? [];
    }
}
