<?php

namespace App\Rules\Friend;

use App\Exceptions\ApiExceptions\Auth\UserNotFoundException;
use App\Exceptions\ApiExceptions\Friend\AlreadyFriendsException;
use App\Exceptions\ApiExceptions\Friend\InvitationAlreadySentException;
use App\Exceptions\ApiExceptions\Friend\InvitingYourselfException;
use App\Exceptions\ApiExceptions\Friend\UserWaitsForYourAccept;
use App\Models\Friend;
use App\Models\User;
use Illuminate\Contracts\Validation\Rule;

class PhoneRule implements Rule
{
    /**
     * 0 - all OK
     * 1 - field is not positive int
     * 2 - not 9 chars long
     * 3 - no user with such phone
     * 4 - user is trying to invite himself
     * 5 - receiver already befriended.
     */
    private $error_code = 0;

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
     * @param string $attribute
     * @param mixed $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        if (!ctype_digit($value)) {
            $this->error_code = 1;

            return false;
        }

        if (strlen($value) != 9) {
            $this->error_code = 2;

            return false;
        }

        $dbFriend = User::where('phone', $value)->first();

        if (!isset($dbFriend)) {
            $this->error_code = 3;
            throw new UserNotFoundException();
        }

        $current_user_id = auth()->user()->id;
        if ($dbFriend->id == $current_user_id) {
            $this->error_code = 4;

            throw new InvitingYourselfException();
        }

        $friend = Friend::where('sender_id', $current_user_id)
            ->where('receiver_id', $dbFriend->id)
            ->where('status', 0)
            ->first();

        if ($friend) {
            throw new InvitationAlreadySentException();
        }

        $friend = Friend::where('sender_id', $dbFriend->id)
            ->where('receiver_id', $current_user_id)
            ->where('status', 0)
            ->first();

        if ($friend) {
            throw new UserWaitsForYourAccept();
        }

        $friends = Friend::where(function ($query) use ($current_user_id, $dbFriend) {
            $query->whereIn('sender_id', [$current_user_id, $dbFriend->id])
                ->whereIn('receiver_id', [$current_user_id, $dbFriend->id]);
        })->where('status', 1)->first();

        if ($friends) {
            throw new AlreadyFriendsException();
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
        switch ($this->error_code) {
            case 1:
                return 'Field \'phone\' isn\'t positive int';
            case 2:
                return 'Field \'phone\' must be 9 characters long';
            case 3:
                return 'No user with such phone number';
            case 4:
                return 'You can\'t invite yourself, silly :)';
            case 5:
                return 'You already befriended that user';
            default:
                return 'Unknown error: ' . $this->error_code;
        }
    }
}
