<?php

namespace App\Rules\Friend;

use App\Models\Friend;
use App\Models\User;
use Illuminate\Contracts\Validation\Rule;

class StatusRule implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $currentUserId = auth()->user()->id;
        var_dump($attribute);
        echo $this;
        dd($currentUserId);
        // Friend::where()

        $friend = User::where('email', $value)->first();
        if (! isset($friend)) {
            $this->error_code = 2;

            return false;
        }

        $current_user_id = auth()->user()->id;
        if ($friend->id == $current_user_id) {
            $this->error_code = 3;

            return false;
        }

        if (Friend::where(function ($query) use ($friend, $current_user_id) {
            $query->where('sender_id', $friend->id)->where('receiver_id', $current_user_id);
        })->orWhere(function ($query) use ($friend, $current_user_id) {
            $query->where('receiver_id', $friend->id)->where('sender_id', $current_user_id);
        })->exists()) {
            $this->error_code = 4;

            return false;
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
        switch($this->error_code) {
            case 1: return 'Field \'email\' doesn\'t seem to be an email';
            case 2: return 'No user with such email found';
            case 3: return 'You can\'t invite yourself, silly :)';
            case 4: return 'You already befriended that user';
            default: return 'Unknown error: '.$this->error_code;
        }

        return 'The validation error message.';
    }
}
