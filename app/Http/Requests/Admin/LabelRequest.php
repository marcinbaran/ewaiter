<?php

namespace App\Http\Requests\Admin;

use App\Http\Requests\RequestTrait;
use App\Rules\UniqueJsonField;
use Illuminate\Foundation\Http\FormRequest;

class LabelRequest extends FormRequest
{
    use RequestTrait;

    /**
     * @var array
     */
    private static $rules = [
        'admin.labels.store' => [
            'name' => [
                'required',
                'array',
                'min:1',
                'max:50',
            ],
            'name.*' => [
                'nullable',
                'string',
                'min:3',
                'max:100',
            ],
            'name.pl' => [
                'required',
                'string',
                'min:3',
                'max:100',
            ],
            'icon' => 'string',
        ],
        'admin.labels.update' => [
            'name' => [
                'required',
                'array',
                'min:1',
                'max:50',
            ],
            'name.*' => [
                'nullable',
                'string',
                'min:3',
                'max:100',
            ],
            'name.pl' => [
                'required',
                'string',
                'min:3',
                'max:100',
            ],
            'icon' => 'string',
        ],
    ];

    public function attributes()
    {
        return [
            'name' => __('validation.labels.name'),
            'icon' => __('validation.labels.icon'),
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
        $labelId = $this->route()->parameters['id'] ?? null;
        foreach (['name.pl', 'name.*'] as $field) {
            if ($this->route()->getName() === 'admin.labels.update') {
                $rules[$field][] = new UniqueJsonField('label', 'name', $labelId);
            } else {
                $rules[$field][] = new UniqueJsonField('label', 'name');
            }
        }

        return $rules;
    }
}
