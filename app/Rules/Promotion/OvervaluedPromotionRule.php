<?php

namespace App\Rules\Promotion;

use App\Enum\PromotionType;
use App\Enum\PromotionValueType;
use App\Models\Dish;
use App\Models\Promotion;
use Closure;
use Illuminate\Contracts\Validation\DataAwareRule;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Collection;

class OvervaluedPromotionRule implements ValidationRule, DataAwareRule
{
    /**
     * All of the data under validation.
     *
     * @var array<string, mixed>
     */
    protected $data = [];

    private int $minimalDishPrice;

    private int $minimalPromotionPercentageValue;

    private int $maximalPromotionPercentageValue;

    private PromotionType $promotionType;

    private PromotionValueType $promotionValueType;
    // ...

    /**
     * Set the data under validation.
     *
     * @param array<string, mixed> $data
     */
    public function setData(array $data): static
    {
        $this->data = $data;

        $this->promotionType = PromotionType::from($this->data['type']);
        $this->promotionValueType = PromotionValueType::from($this->data['typeValue']);

        $this->minimalDishPrice = config('app.minimal_dish_price', 1);
        $this->minimalPromotionPercentageValue = config('app.minimal_promotion_percentage_value', 1);
        $this->maximalPromotionPercentageValue = config('app.maximal_promotion_percentage_value', 99);

        return $this;
    }

    /**
     * Run the validation rule.
     *
     * @param \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $promotionType = PromotionType::from($this->data['type']);
        $promotionValidationMessage = null;

        if ($promotionType === PromotionType::DISH) {
            $promotionDishes = collect([Dish::query()->where('id', $this->data['orderDish']['id'])->first()]);
            $promotionValidationMessage = $this->isDishPromotionValid($promotionDishes);
        } elseif ($promotionType === PromotionType::CATEGORY) {
            $promotionDishes = Dish::query()->where('food_category_id', $this->data['orderCategory']['id'])->get();
            $promotionValidationMessage = $this->isCategoryPromotionValid($promotionDishes);
        } elseif ($promotionType === PromotionType::BUNDLE) {
            $promotionDishes = Dish::query()->whereIn('id', $this->data['orderDishes'])->get();
            $promotionValidationMessage = $this->isBundlePromotionValid($promotionDishes);
        }

        if ($promotionValidationMessage !== null) {
            $fail($promotionValidationMessage);
        }
    }

    private function getPromotionId()
    {
        $promotion = request()->route()->parameter('promotion');

        return $promotion instanceof Promotion ? $promotion->id : 0;
    }

    private function isPromotionValueValid(Collection $promotionDishes): ?string
    {
        $promotion = $this->data;

        if ($this->promotionValueType === PromotionValueType::PRICE) {
            foreach ($promotionDishes as $dish) {
                if ($dish->price - $promotion['value'] < $this->minimalDishPrice) {
                    return __('validation.promotion.value_too_high');
                }
            }
        }
        if ($this->promotionValueType === PromotionValueType::PERCENTAGE) {
            if ($promotion['value'] > $this->maximalPromotionPercentageValue || $promotion['value'] < $this->minimalPromotionPercentageValue) {
                return __('validation.promotion.percentage_value_not_valid');
            }
            foreach ($promotionDishes as $dish) {
                if ($dish->price - ($dish->price * $promotion['value'] / 100) < $this->minimalDishPrice) {
                    return __('validation.promotion.value_too_high');
                }
            }
        }

        return null;
    }

    private function isDishPromotionValid(Collection $promotionDishes): ?string
    {
        return $this->isPromotionValueValid($promotionDishes);
    }

    private function isCategoryPromotionValid(Collection $promotionDishes): ?string
    {
        return $this->isPromotionValueValid($promotionDishes);
    }

    private function isBundlePromotionValid(Collection $promotionDishes): ?string
    {
        $promotion = $this->data;

        if ($this->data['value'] < $this->minimalDishPrice) {
            return __('validation.promotion.value_too_high');
        }

        return null;
    }
}
