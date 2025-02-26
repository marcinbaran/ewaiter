<?php

namespace App\Rules;

use App\Exceptions\ApiExceptions\Review\CommentIsTooLong;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class CommentTooLongRule implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (strlen($value) > 1000 ) {
            throw new CommentIsTooLong();
        }
    }
}
