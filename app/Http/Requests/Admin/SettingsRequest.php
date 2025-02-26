<?php

namespace App\Http\Requests\Admin;

use App\Http\Requests\RequestTrait;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Request;

class SettingsRequest extends FormRequest
{
    use RequestTrait;

    /**
     * @var array
     */
    private static $rules = [
        'admin.settings.store' => [
            'key' => 'required|string|max:255|unique:tenant.settings,key',
            'value' => [
                'required',
            ],
//            'value_type' => 'required',
            'description' => 'string|nullable',
        ],
        'admin.settings.update' => [
            'key' => 'required|string|max:255|unique:tenant.settings,key',
            'value' => [
                'required',
            ],
//            'value_type' => 'required',
            'description' => 'string|nullable',
        ],
    ];

    public function attributes()
    {
        return [
            'key' => __('validation.settings.key'),
            'value' => __('validation.settings.value'),
            'value_type' => __('validation.settings.value_type'),
            'description' => __('validation.settings.description'),
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

        if ('admin.settings.update' == $this->route()->getName()) {
            $rules['key'] = Rule::unique('tenant.settings', 'key')->ignore($this->settings->id);
        }
        if ($this->settings->key == 'logo') {
            $rules['value'] = array_filter($rules['value'], function ($rule) {
                return ! in_array($rule, ['required']);
            });
        }

        return $rules;
    }
}
