<?php

namespace App\Http\Requests\Admin;

use App\Http\Requests\RequestTrait;
use App\Rules\UniqueJsonField;
use Illuminate\Foundation\Http\FormRequest;

class TagRequest extends FormRequest
{
    use RequestTrait;

    /**
     * @var array
     */
    private static $rules = [
        'admin.tags.store' => [
            'visibility' => 'boolean',
            'name' => [
                'required',
                'array',
                'min:1',
                'max:100',
            ],
            'name.*' => [
                'nullable',
                'string',
                'min:3',
                'max:100',
            ],
            'name.pl'=> [
                'required',
                'string',
                'min:3',
                'max:100',
            ],
            'icon' => 'nullable',
            'description' => 'nullable|array|min:1|max:50',
            'description.*' => 'nullable|string|min:3|max:1000',

        ],
        'admin.tags.update' => [
            'visibility' => 'boolean',
            'name' => [
                'required',
                'array',
                'min:1',
                'max:100',
            ],
            'name.*' => [
                'nullable',
                'string',
                'min:3',
                'max:100',
            ],
            'name.pl'=> [
                'required',
                'string',
                'min:3',
                'max:100',
            ],
            'icon' => 'nullable',
            'description' => 'nullable|array|min:1|max:50',
            'description.*' => 'nullable|string|min:3|max:1000',
        ],
    ];

    public function attributes()
    {
        return [
            'visibility' => __('validation.tag.visibility'),
            'name' => __('validation.tag.name'),
            'description' => __('validation.tag.description'),
            'icon' => __('validation.tag.icon'),
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
        $tagId = $this->route()->parameters['tag']->id ?? null;

        foreach (['name.pl', 'name.*'] as $field) {
            if ('admin.tags.update' == $this->route()->getName()) {
                $rules[$field][] = new UniqueJsonField('tags', 'name', $tagId);
            } else {
                $rules[$field][] = new UniqueJsonField('tags', 'name');
            }
        }

        return $rules;
    }
}
