<?php

namespace App\Rules\DeliveryRange;

use App\Models\DeliveryRange;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class OutOfRangeDeliveryExists implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $isOutOfRange = DeliveryRange::where('out_of_range', true)->first() ? true : false;
        if ($isOutOfRange) {
            $fail(__('validation.delivery_range.out_of_range_exists'));
        }
    }
}
