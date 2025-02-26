<?php

namespace App\Http\Requests\Admin;

use App\Http\Requests\RequestTrait;
use App\Rules\OnlinePaymentProviderAccount\CheckCredential;
use Illuminate\Foundation\Http\FormRequest;

class OnlinePaymentProviderAccountRequest extends FormRequest
{
    use RequestTrait;

    private static $rules = [
        'admin.online_payment_provider_account.store' => [
            'comment' => ['nullable', 'string', 'min:3', 'max:100'],
            'login' => 'required|string|min:3|max:255',
            'password' => 'required|string|min:3|max:255',
            'api_key' => 'required|string|min:3|max:255',
            'api_password' => 'required|string|min:3|max:255',
            'restaurant_id' => 'required|integer|exists:restaurants,id|unique:online_payment_provider_accounts,restaurant_id',
        ],
    ];

    public function attributes()
    {
        return [
            'comment' => __('online_payment_provider_account.form.comment'),
            'login' => __('online_payment_provider_account.form.login'),
            'password' => __('online_payment_provider_account.form.password'),
            'api_key' => __('online_payment_provider_account.form.api_key'),
            'api_password' => __('online_payment_provider_account.form.api_password'),
            'restaurant_id' => __('online_payment_provider_account.form.restaurant'),
        ];
    }

    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $name = $this->route()->getName();
        $rules = self::$rules[$name] ?? [];
        if (in_array($name, ['admin.online_payment_provider_account.store'])) {
            $rules['comment'][] = new CheckCredential();
        }
        return $rules;
    }
}
