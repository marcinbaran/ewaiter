<?php

namespace App\Http\Requests\Admin;

use App\Http\Requests\RequestTrait;
use Illuminate\Foundation\Http\FormRequest;

class StatisticRequest extends FormRequest
{
    use RequestTrait;

    /**
     * @var array
     */
    private static $rules = [
        self::METHOD_GET => [
            'table' => 'array|min:1',
            'table.*' => 'integer|min:1',
            'dish' => 'array|min:1',
            'dish.*' => 'integer|min:1',
            'status' => 'array|min:1',
            'status.*' => 'integer|min:0|max:4',
            'createdAt' => 'array|min:1|max:2',
            'createdAt.*' => 'nullable|date',
            'group' => 'string|in:dayname,monthname,year,time',
            'order.createdAt' => 'string|in:asc,desc',
            'order.updatedAt' => 'string|in:asc,desc',
            'order.price' => 'string|in:asc,desc',
            'order.quantity' => 'string|in:asc,desc',
            'order.discount' => 'string|in:asc,desc',
            'paid' => 'boolean',
        ],
    ];

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
