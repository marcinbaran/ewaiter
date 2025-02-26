<?php

namespace App\Http\Requests\Admin;

use App\Http\Requests\RequestTrait;
use App\Rules\Promotion\ExistPromotionRule;
use App\Rules\Promotion\OvervaluedPromotionRule;
use Illuminate\Foundation\Http\FormRequest;

class PromotionRequest extends FormRequest
{
    use RequestTrait;

    private static $rules = [
        'admin.promotions.store' => [
            'type' => 'required|integer|min:0|max:3',
            'typeValue' => [
                'required',
                'integer',
                'min:0',
                'max:1',
            ],
            'value' => [
                'required',
                'numeric',
                'min:1',
            ],
            'orderDish.id' => 'required_if:type,0|min:1|exists:tenant.dishes,id',
            'orderCategory.id' => 'required_if:type,2|min:1|exists:tenant.food_categories,id',
            'orderDishes' => 'required_if:type,3|array|min:2',
            'orderDishes.*.id' => 'required_if:type,3|min:1|exists:tenant.dishes,id',
            'name.*' => 'nullable|string|min:3|max:100',
            'name.pl' => 'string|min:3|max:100',
            'description.*' => 'nullable|string|min:3|max:1000',
            'startAt' => 'nullable|date|before_or_equal:endAt',
            'endAt' => 'nullable|date|after_or_equal:startAt',
            'merge' => 'boolean',
            'active' => 'boolean',
        ],
        'admin.promotions.update' => [
            'type' => 'required|integer|min:0|max:3',
            'typeValue' => [
                'required',
                'integer',
                'min:0',
                'max:1',
            ],
            'value' => [
                'required',
                'numeric',
                'min:0',
            ],
            'orderDish.id' => 'required_if:type,0|min:1|exists:tenant.dishes,id',
            'orderCategory.id' => 'required_if:type,2|min:1|exists:tenant.food_categories,id',
            'orderDishes' => 'required_if:type,3|array|min:2',
            'orderDishes.*.id' => 'required_if:type,3|min:1|exists:tenant.dishes,id',
            'name.*' => 'nullable|string|min:3|max:100',
            'name.pl' => 'string|min:3|max:100',
            'description.*' => 'nullable|string|min:3|max:1000',
            'startAt' => 'nullable|date|before_or_equal:endAt|required_with:endAt',
            'endAt' => 'nullable|date|after_or_equal:startAt|required_with:startAt',
            'merge' => 'boolean',
            'active' => 'boolean',
        ],
    ];

    public function attributes(): array
    {
        return [
            'type' => __('validation.promotion.type'),
            'typeValue' => __('validation.promotion.type_value'),
            'value' => __('validation.promotion.value'),
            'orderDish.id' => __('validation.promotion.dish'),
            'orderCategory.id' => __('validation.promotion.category'),
            'orderDishes' => __('validation.promotion.dishes'),
            'orderDishes.*.id' => __('validation.promotion.dishes'),
            'name' => __('validation.promotion.name'),
            'name_locale.*' => __('validation.promotion.name'),
            'name.*' => __('validation.promotion.name'),
            'description' => __('validation.promotion.description'),
            'description_locale.*' => __('validation.promotion.description'),
            'description.*' => __('validation.promotion.description'),
            'startAt' => __('validation.promotion.start_at'),
            'endAt' => __('validation.promotion.end_at'),
            'merge' => __('validation.promotion.merge'),
            'active' => __('validation.promotion.active'),
        ];
    }

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $rules = self::$rules[$this->route()->getName()] ?? [];
        $rules['typeValue'][] = new ExistPromotionRule();
        $rules['value'][] = new OvervaluedPromotionRule();

        return $rules;
    }
}
