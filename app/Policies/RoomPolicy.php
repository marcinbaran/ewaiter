<?php

namespace App\Policies;

use App\Models\Room;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class RoomPolicy
{
    use HandlesAuthorization;

    /**
     * before function.
     *
     * @param $user
     *
     * @return bool
     * */
    public function before($user)
    {
        return $user->isOne([User::ROLE_MANAGER, User::ROLE_ADMIN]);
    }

    /**
     * Determine whether the user can view the room.
     *
     * @param User  $user
     * @param Room $room
     *
     * @return bool
     */
    public function view(User $user, Room $room): bool
    {
        return true;
    }

    /**
     * Determine whether the user can create rooms.
     *
     * @param User $user
     *
     * @return bool
     */
    public function create(User $user): bool
    {
        return $user->isOne([User::ROLE_MANAGER, User::ROLE_ADMIN]);
    }

    /**
     * Determine whether the user can update the room.
     *
     * @param User  $user
     * @param Room $room
     *
     * @return bool
     */
    public function update(User $user, Room $room): bool
    {
        return $user->isOne([User::ROLE_MANAGER, User::ROLE_ADMIN]);
    }

    /**
     * Determine whether the user can delete the room.
     *
     * @param User  $user
     * @param Room $room
     *
     * @return bool
     */
    public function delete(User $user, Room $room): bool
    {
        return $user->isOne([User::ROLE_MANAGER, User::ROLE_ADMIN]);
    }
}
