<?php

namespace App\Policies;

use App\Models\Attribute;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class AttributePolicy
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
        if ($user->isOne([User::ROLE_MANAGER, User::ROLE_ADMIN])) {
            return true;
        }
    }

    /**
     * Determine whether the user can view the attribute.
     *
     * @param User     $user
     * @param Attribute $attribute
     *
     * @return bool
     */
    public function view(User $user, Attribute $attribute): bool
    {
        return true;
    }

    /**
     * Determine whether the user can create attribute.
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
     * Determine whether the user can update the attribute.
     *
     * @param User     $user
     * @param Attribute $attribute
     *
     * @return bool
     */
    public function update(User $user, Attribute $attribute): bool
    {
        return false;
    }

    /**
     * Determine whether the user can delete the attribute.
     *
     * @param User     $user
     * @param Attribute $attribute
     *
     * @return bool
     */
    public function delete(User $user, Attribute $attribute): bool
    {
        return false;
    }
}
