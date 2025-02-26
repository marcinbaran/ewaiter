<?php

namespace App\Policies;

use App\Models\Notification;
use App\Models\User;
use App\Models\UserSystem;
use Illuminate\Auth\Access\HandlesAuthorization;

class NotificationPolicy
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
    }

    /**
     * Determine whether the user can view the bill.
     *
     * @param UserSystem $user
     * @param Bill $bill
     *
     * @return bool
     */
    public function view(UserSystem $user, Notification $notification): bool
    {
        return true;
    }

    /**
     * Determine whether the user can create bills.
     *
     * @param UserSystem $user
     *
     * @return bool
     */
    public function create(UserSystem $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can update the bill.
     *
     * @param UserSystem $user
     * @param Bill $bill
     *
     * @return bool
     */
    public function update(UserSystem $user, Notification $notification): bool
    {
        return true;
    }

    /**
     * Determine whether the user can delete the bill.
     *
     * @param UserSystem $user
     * @param Bill $bill
     *
     * @return bool
     */
    public function delete(UserSystem $user, Notification $notification): bool
    {
        return $user->isOne([User::ROLE_ADMIN, User::ROLE_MANAGER]);
    }
}
