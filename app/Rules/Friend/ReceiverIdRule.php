<?php

namespace App\Rules\Friend;

use App\Exceptions\ApiExceptions\Friend\AlreadyFriendsException;
use App\Exceptions\ApiExceptions\Friend\FriendUserNotFound;
use App\Exceptions\ApiExceptions\Friend\InvitationAlreadySentException;
use App\Exceptions\ApiExceptions\Friend\InvitingYourselfException;
use App\Exceptions\ApiExceptions\Friend\ReceiverIdMustBeInteger;
use App\Exceptions\ApiExceptions\Friend\UserWaitsForYourAccept;
use App\Models\Friend;
use App\Models\User;
use Illuminate\Contracts\Validation\Rule;

class ReceiverIdRule implements Rule
{
    /**
     * 0 - all OK
     * 1 - field is not positive int
     * 2 - no user of such ID
     * 3 - user is trying to invite himself
     * 4 - receiver already befriended.
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
            throw new ReceiverIdMustBeInteger();
        }

        $dbFriend = User::where('id', $value)->first();

        if (!$dbFriend->exists()) {
            throw new FriendUserNotFound();
        }

        $current_user_id = auth()->user()->id;
        if ($current_user_id == $value) {
            throw new InvitingYourselfException();
        }

        $friend = Friend::where('sender_id', $current_user_id)
            ->where('receiver_id', $dbFriend->id)
            ->where('status', 0)
            ->first();

        if ($friend) {
            throw new InvitationAlreadySentException();
        }

        $friend = Friend::where('receiver_id', $current_user_id)
            ->where('sender_id', $dbFriend->id)
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
                return '\'receiver_id\' must be a positive integer';
                break;
            case 2:
                return 'Receiver of such ID not found';
                break;
            case 3:
                return 'You can\'t invite yourself, silly :)';
                break;
            case 4:
                return 'You already befriended that user';
                break;
            default:
                return 'Unknown error';
                break;
        }
    }
}
