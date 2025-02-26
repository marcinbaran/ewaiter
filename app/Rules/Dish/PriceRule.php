<?php

namespace App\Rules\Dish;

use Closure;
use Illuminate\Contracts\Validation\DataAwareRule;
use Illuminate\Contracts\Validation\ValidationRule;

class PriceRule implements ValidationRule, DataAwareRule
{
    /**
     * All of the data under validation.
     *
     * @var array<string, mixed>
     */
    protected $data = [];

    // ...

    /**
     * Set the data under validation.
     *
     * @param array<string, mixed> $data
     */
    public function setData(array $data): static
    {
        $this->data = $data;

        return $this;
    }

    /**
     * Run the validation rule.
     *
     * @param \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $dishPrice = $this->data['price'];
        $minDishPrice = config('app.minimal_dish_price', 1);
        $maxDishPrice = config('app.maximal_dish_price', 999999.99);
        $validationMessage = __('validation.between.numeric', [
            'attribute' => __('validation.dish.'.$attribute),
            'min' => $minDishPrice,
            'max' => $maxDishPrice,
        ]);
        if ($dishPrice < $minDishPrice || $dishPrice > $maxDishPrice) {
            $fail($validationMessage);
        }
    }
}
