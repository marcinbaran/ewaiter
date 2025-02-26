<?php

namespace App\Rules\Worktime;

use App\Models\Worktime;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class SingleWorktime implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $count = Worktime::where($attribute, $value)->count();

        if ($count > 0) {
            $fail(__('errors.worktime_already_exists'));
        }
    }
}
