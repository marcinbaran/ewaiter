<?php

namespace App\Rules\User;

use App\Exceptions\ApiExceptions\Auth\PhoneNumberAlreadyExistsException;
use App\Models\User;
use Closure;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Contracts\Validation\ValidationRule;

class SetPhoneRule implements Rule
{
    /**
     * 0 - all OK
     * 1 - user with such phone already exists
     * 2 - phone is not valid
     */
    private $error_code = 'Unknown error';

    public function passes($attribute, mixed $value): bool
    {
        if (strlen($value) != 9) {
            $this->error_code = "Phone number is not valid";
            return false;
        }

        $user = User::where('phone', $value)->first();
        if ($user) {
            throw new PhoneNumberAlreadyExistsException();
        }
        return true;
    }


    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return $this->error_code;
    }
}
