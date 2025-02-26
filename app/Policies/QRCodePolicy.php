<?php

namespace App\Policies;

use App\Models\QRCode;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class QRCodePolicy
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
     * Determine whether the user can view the room.
     *
     * @param User  $user
     * @param QRCode $qr_code
     *
     * @return bool
     */
    public function view(User $user): bool
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
     * @param QRCode $qr_code
     *
     * @return bool
     */
    public function update(User $user, QRCode $qr_code): bool
    {
        return $user->isOne([User::ROLE_MANAGER, User::ROLE_ADMIN]);
    }

    /**
     * Determine whether the user can delete the room.
     *
     * @param User  $user
     * @param QRCode $qr_code
     *
     * @return bool
     */
    public function delete(User $user, QRCode $qr_code): bool
    {
        return $user->isOne([User::ROLE_MANAGER, User::ROLE_ADMIN]);
    }
}
