<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Worktime;
use Illuminate\Auth\Access\HandlesAuthorization;

class WorktimePolicy
{
    use HandlesAuthorization;

    /**
     * before function.
     *
     * @param User $user
     *
     * @return bool
     * */
    public function before($user)
    {
        if ($user->isOne([User::ROLE_MANAGER, User::ROLE_ADMIN])) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can view the bill.
     *
     * @param User $user
     * @param Worktime $worktime
     *
     * @return bool
     */
    public function view(User $user, Worktime $worktime): bool
    {
        return true;
    }

    /**
     * Determine whether the user can create bills.
     *
     * @param User $user
     *
     * @return bool
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can update the bill.
     *
     * @param User $user
     * @param Worktime $worktime
     *
     * @return bool
     */
    public function update(User $user, Worktime $worktime): bool
    {
        return false;
    }

    /**
     * Determine whether the user can delete the bill.
     *
     * @param User $user
     * @param Worktime $worktime
     *
     * @return bool
     */
    public function delete(User $user, Worktime $worktime): bool
    {
        return $user->isOne([User::ROLE_ADMIN, User::ROLE_MANAGER]);
    }
}
