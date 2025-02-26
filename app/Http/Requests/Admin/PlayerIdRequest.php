<?php

namespace App\Http\Requests\Admin;

use App\Http\Requests\RequestTrait;
use Illuminate\Foundation\Http\FormRequest;

class PlayerIdRequest extends FormRequest
{
    use RequestTrait;

    /**
     * @var array
     */
    private static $rules = [
        'admin.users.store' => [
            'playerId' => 'required|string|max:250',
            'user.id' => 'integer|exists:tenant.users,id',
            'deviceInfo' => 'string',
        ],
        'admin.users.update' => [
            'playerId' => 'string|max:250',
            'user.id' => 'integer|exists:tenant.users,id',
            'deviceInfo' => 'string',
        ],
    ];

    public function attributes()
    {
        return [
            'playerId' => __('validation.player_id.player'),
            'user.id' => __('validation.player_id.user'),
            'deviceInfo' => __('validation.player_id.device_info'),
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
