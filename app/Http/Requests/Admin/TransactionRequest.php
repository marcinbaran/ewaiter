<?php

namespace App\Http\Requests\Admin;

use App\Http\Requests\RequestTrait;
use Illuminate\Foundation\Http\FormRequest;

class TransactionRequest extends FormRequest
{
    use RequestTrait;

    /**
     * @var array
     */
    private static $rules = [
        'admin.tags.store' => [
            'name' => 'required|string|max:255',
            'tag' => 'required|string|max:255|unique:tenant.tags',
            'description' => 'nullable|string',
            'icon' => 'nullable',
            'visibility' => 'boolean',
        ],
        'admin.tags.update' => [
            'name' => 'required|string|max:255',
            'tag' => 'required|string|max:255',
            'description' => 'nullable|string',
            'icon' => 'nullable',
            'visibility' => 'boolean',
        ],
    ];

    public function attributes()
    {
        return [
            'name' => __('validation.name'),
            'tag' => __('validation.tag'),
            'description' => __('validation.description'),
            'icon' => __('validation.icon'),
            'visibility' => __('validation.visibility'),
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
        if ('admin.users.update' == $this->route()->getName()) {
            $rules['tag'][] = Rule::unique('tenant.tags', 'tag')->ignore($this->id);
        }

        return self::$rules[$this->route()->getName()] ?? [];
    }
}
