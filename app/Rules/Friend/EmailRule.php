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

class EmailRule implements Rule
{
    /**
     * 0 - all OK
     * 1 - value doesn't have proper email format
     * 2 - no such user
     * 3 - befriending yourself
     * 4 - user already befriended.
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
        if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
            return false;
        }

        $dbFriend = User::where('email', $value)->first();

        if (!isset($dbFriend)) {
            throw new UserNotFoundException();
        }

        $current_user_id = auth()->user()->id;
        if ($dbFriend->id == $current_user_id) {
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
    public function message(): string
    {
        switch ($this->error_code) {
            case 1:
                return 'Field \'email\' doesn\'t seem to be an email';
            case 2:
                return 'No user with such email found';
            case 3:
                return 'You can\'t invite yourself, silly :)';
            case 4:
                return 'You already befriended that user';
            default:
                return 'Unknown error: ' . $this->error_code;
        }

        return 'The validation error message.';
    }
}
