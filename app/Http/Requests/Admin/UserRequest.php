<?php

namespace App\Http\Requests\Admin;

use App\Helpers\FormHelper;
use App\Http\Requests\RequestTrait;
use App\Models\Restaurant;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UserRequest extends FormRequest
{
    use RequestTrait;

    /**
     * @var array
     */
    private static $rules = [
        'admin.register_save' => [
            'first_name' => 'required|max:100',
            'last_name' => 'required|max:100',
            'login' => [
                'nullable',
                'string',
                'max:100',
                'unique:users',
            ],
            'email' => [
                'required',
                'email',
                'max:250',
                'unique:users',
            ],
            'phone' => 'required|string|max:15|unique:users',
            'password' => 'required|confirmed|min:6|max:100',
            'res_id' => 'nullable|integer',
        ],
        'admin.register_auth_save' => [
            'auth_code' => 'required|max:20',
        ],
        'admin.users.store' => [
            'first_name' => 'required|min:3|max:100',
            'last_name' => 'nullable|min:3|max:100',
            'login' => [
                'nullable',
                'string',
                'min:3',
                'max:100',
                'unique:tenant.users',
            ],
            'email' => [
                'required',
                'email',
                'min:3',
                'max:100',
                'unique:tenant.users',
            ],
            'phone' => [
                'required',
                'string',
                'size:9',
            ],
            'roles' => 'array|min:1',
            'roles.*.id' => 'string',
            'blocked' => 'boolean',
            'isRoom' => 'boolean',
            'password' => 'required|confirmed|min:8|max:100',
        ],
        'admin.users.update' => [
            'first_name' => 'required|min:3|max:100',
            'last_name' => 'nullable|min:3|max:100',
            'login' => [
                'nullable',
                'string',
                'min:3',
                'max:100',
            ],
            'email' => [
                'required',
                'email',
                'min:3',
                'max:100',
            ],
            'phone' => [
                'required',
                'string',
                'size:9',
            ],
            'roles' => 'array|min:1',
            'roles.*.id' => 'string',
            'blocked' => 'boolean',
            'isRoom' => 'boolean',
            'password' => 'nullable|confirmed|min:8|max:100',
        ],
    ];

    public function attributes()
    {
        return [
            'first_name' => __('validation.user.first_name'),
            'last_name' => __('validation.user.last_name'),
            'login' => __('validation.user.login'),
            'email' => __('validation.user.email'),
            'phone' => __('validation.user.phone'),
            'roles' => __('validation.user.roles'),
            'blocked' => __('validation.user.blocked'),
            'isRoom' => __('validation.user.isRoom'),
            'password' => __('validation.user.password'),
        ];
    }

    public function authorize(): bool
    {
        return true;
    }

    public function prepareForValidation()
    {
        $this->merge([
            'phone' => FormHelper::formatPhoneNumber($this->phone),
        ]);
    }

    public function rules(): array
    {
        $isTenant = Restaurant::getCurrentRestaurant() !== null;
        $table = $isTenant ? 'tenant.users' : 'users';
        $rules = self::$rules[$this->route()->getName()] ?? [];

        if ('admin.users.store' == $this->route()->getName()) {
            $rules['phone'][] = 'unique:'.$table;
        }

        if ('admin.users.update' == $this->route()->getName()) {
            $rules['email'][] = Rule::unique('tenant.users', 'email')->ignore($this->user->id);
            $rules['login'][] = Rule::unique('tenant.users', 'login')->ignore($this->user->id);
            $rules['phone'][] = Rule::unique($table, 'phone')->ignore($this->user->id);
        }

        return $rules;
    }
}
