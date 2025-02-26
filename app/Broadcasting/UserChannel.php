<?php

namespace App\Broadcasting;

use App\Models\User;
use Laravel\Reverb\Connection;
use function Symfony\Component\Translation\t;

class UserChannel
{
    /**
     * Create a new channel instance.
     */
    public function __construct()
    {
    }

    /**
     * Authenticate the user's access to the channel.
     *
     * @param  \App\Models\User  $user
     * @param  int  $id
     * @return array|bool
     */
    public function join(User $user, int $userId): array|bool
    {
        if (!Auth::check()) {
            Log::error('UserChannel:join:unauthorized auth ');
            return false;
        }

        if ((int) $user->id !== (int) $userId) {
            Log::error('UserChannel:join:unauthorized id');
            return false;
        }


        return true;
    }

}
