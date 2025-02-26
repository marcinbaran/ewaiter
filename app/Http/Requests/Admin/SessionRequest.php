<?php

namespace App\Http\Requests\Admin;

use App\Http\Requests\RequestTrait;
use Illuminate\Foundation\Http\FormRequest;

class SessionRequest extends FormRequest
{
    use RequestTrait;

    /**
     * @var array
     */
    private static $rules = [
        self::METHOD_POST => [
            'email' => 'required|email',
            'password' => 'required|string|min:6|max:100',
        ],
    ];

    public function attributes()
    {
        return [
            'email' => __('validation.session.email'),
            'password' => __('validation.session.password'),
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
        return self::$rules[$this->getMethod()] ?? [];
    }
}
