<?php

namespace App\Http\Requests\Admin;

use App\Http\Requests\RequestTrait;
use App\Rules\Table\MaxNumberForBatchCreatingTables;
use Illuminate\Foundation\Http\FormRequest;

class TableRequest extends FormRequest
{
    use RequestTrait;

    /**
     * @var array
     */
    private static $rules = [
        'admin.tables.store' => [
            'adding_type' => 'required|in:1,2',
            'name' => 'required_if:adding_type,single|nullable|string|min:3|max:100',
            'number' => 'required_if:adding_type,single|nullable|string|min:1|max:100',
            'from_number' => 'required_if:adding_type,range|nullable|integer|min:1|max:10000|lte:to_number',
            'to_number' => [
                'required_if:adding_type,range',
                'nullable',
                'integer',
                'min:1',
                'max:10000',
                'gte:from_number',
            ],
            'people_number' => 'nullable|integer|min:1|max:100',
            'description' => 'nullable|string|min:3|max:1000',
            'active' => 'boolean',
        ],
        'admin.tables.update' => [
            'name' => 'required|string|min:3|max:100',
            'number' => 'required|string|min:1|max:100',
            'people_number' => 'required|integer|min:1|max:100',
            'description' => 'nullable|string|min:3|max:1000',
            'active' => 'boolean',
        ],
    ];

    public function attributes()
    {
        return [
            'adding_type' => __('validation.table.adding_type'),
            'name' => __('validation.table.name'),
            'number' => __('validation.table.number'),
            'from_number' => __('validation.table.from_number'),
            'to_number' => __('validation.table.to_number'),
            'people_number' => __('validation.table.people_number'),
            'description' => __('validation.table.description'),
            'active' => __('validation.table.active'),
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
        $rules['to_number'][] = new MaxNumberForBatchCreatingTables();

        return $rules;
    }
}
