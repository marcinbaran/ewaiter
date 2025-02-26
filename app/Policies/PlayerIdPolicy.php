<?php

namespace App\Policies;

use App\Models\PlayerId;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class PlayerIdPolicy
{
    use HandlesAuthorization;

    /**
     * before function.
     *
     * @param $user
     *
     * @return bool
     * */
    public function before($user): bool
    {
        if ($user->isOne([User::ROLE_MANAGER, User::ROLE_ADMIN])) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can view the playerId.
     *
     * @param User     $user
     * @param PlayerId $playerId
     *
     * @return bool
     */
    public function view(User $user, PlayerId $playerId): bool
    {
        return false;
    }

    /**
     * Determine whether the user can create playerIdes.
     *
     * @param User $user
     *
     * @return bool
     */
    public function create(User $user): bool
    {
        return false;
    }

    /**
     * Determine whether the user can update the playerId.
     *
     * @param User     $user
     * @param PlayerId $playerId
     *
     * @return bool
     */
    public function update(User $user, PlayerId $playerId): bool
    {
        return false;
    }

    /**
     * Determine whether the user can delete the playerId.
     *
     * @param User     $user
     * @param PlayerId $playerId
     *
     * @return bool
     */
    public function delete(User $user, PlayerId $playerId): bool
    {
        return false;
    }
}
