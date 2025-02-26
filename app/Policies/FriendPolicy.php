<?php

namespace App\Policies;

use App\Models\Friend;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class FriendPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view the friend.
     *
     * @param  \App\User  $user
     * @param  \App\Friend  $friend
     * @return mixed
     */
    public function view(User $user, Friend $friend)
    {
        //
    }

    /**
     * Determine whether the user can create friends.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        return true;
    }

    /**
     * Determine whether the user can update the friend.
     *
     * @param  \App\User  $user
     * @param  \App\Friend  $friend
     * @return mixed
     */
    public function update(User $user, Friend $friend)
    {
        //
    }

    /**
     * Determine whether the user can delete the friend.
     *
     * @param  \App\User  $user
     * @param  \App\Friend  $friend
     * @return mixed
     */
    public function delete(User $user, Friend $friend)
    {
        //
    }

    /**
     * Determine whether the user can restore the friend.
     *
     * @param  \App\User  $user
     * @param  \App\Friend  $friend
     * @return mixed
     */
    public function restore(User $user, Friend $friend)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the friend.
     *
     * @param  \App\User  $user
     * @param  \App\Friend  $friend
     * @return mixed
     */
    public function forceDelete(User $user, Friend $friend)
    {
        //
    }
}
