<?php

namespace App\Policies;

use App\Models\Address;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class AddressPolicy
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
     * Determine whether the user can view the address.
     *
     * @param User $user
     * @param Address $address
     *
     * @return bool
     */
    public function view(User $user, Address $address): bool
    {
        return true;
    }

    /**
     * Determine whether the user can create addresses.
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
     * Determine whether the user can update the address.
     *
     * @param User $user
     * @param Address $address
     *
     * @return bool
     */
    public function update(User $user, Address $address): bool
    {
        return true;
    }

    /**
     * Determine whether the user can delete the address.
     *
     * @param User $user
     * @param Address $address
     *
     * @return bool
     */
    public function delete(User $user, Address $address): bool
    {
        return $user->isOne([User::ROLE_ADMIN, User::ROLE_MANAGER]);
    }
}
