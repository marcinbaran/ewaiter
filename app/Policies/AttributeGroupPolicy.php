<?php

namespace App\Policies;

use App\Models\AttributeGroup;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class AttributeGroupPolicy
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
     * Determine whether the user can view the attribute groups.
     *
     * @param User     $user
     * @param AttributeGroup $attributeGroup
     *
     * @return bool
     */
    public function view(User $user, AttributeGroup $attributeGroup): bool
    {
        return true;
    }

    /**
     * Determine whether the user can create attribute groups.
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
     * Determine whether the user can update the attribute group.
     *
     * @param User     $user
     * @param AttributeGroup $attributeGroup
     *
     * @return bool
     */
    public function update(User $user, AttributeGroup $attributeGroup): bool
    {
        return false;
    }

    /**
     * Determine whether the user can delete the attribute group.
     *
     * @param User     $user
     * @param AttributeGroup $attributeGroup
     *
     * @return bool
     */
    public function delete(User $user, AttributeGroup $attributeGroup): bool
    {
        return false;
    }
}
