<?php

namespace App\Http\Requests\Admin;

use App\Http\Requests\RequestTrait;
use Illuminate\Foundation\Http\FormRequest;

class RatingRequest extends FormRequest
{
    use RequestTrait;

    /**
     * @var array
     */
    private static $rules = [
        'admin.ratings.store' => [
//            'bill_id' => 'required|integer',
//            'restaurant_id' => 'required|integer|exists:restaurants,id',
            'comment' => 'nullable|string',
            'restaurant_comment' => 'nullable|string',
            'r_food' => 'nullable|integer|between:1,5',
            'r_delivery' => 'nullable|integer|between:1,5',
            'anonymous' => 'boolean',
            'visibility' => 'boolean',
        ],
        'admin.ratings.update' => [
//            'bill_id' => 'required|integer',
//            'restaurant_id' => 'required|integer|exists:restaurants,id',
            'comment' => 'nullable|string',
            'restaurant_comment' => 'nullable|string',
            'r_food' => 'nullable|integer|between:1,5',
            'r_delivery' => 'nullable|integer|between:1,5',
            'anonymous' => 'boolean',
            'visibility' => 'boolean',
        ],
    ];

    public function attributes()
    {
        return [
            'comment' => __('validation.comment'),
            'restaurant_comment' => __('validation.restaurant_comment'),
            'r_food' => __('validation.r_food'),
            'r_delivery' => __('validation.r_delivery'),
            'anonymous' => __('validation.anonymous'),
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
        return self::$rules[$this->route()->getName()] ?? [];
    }
}
