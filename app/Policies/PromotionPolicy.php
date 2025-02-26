<?php

namespace App\Policies;

use App\Models\Promotion;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class PromotionPolicy
{
    use HandlesAuthorization;

    /**\
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
     * Determine whether the user can view the promotion.
     *
     * @param User      $user
     * @param Promotion $promotion
     *
     * @return bool
     */
    public function view(User $user, Promotion $promotion): bool
    {
        if ($user->isOne([User::ROLE_MANAGER, User::ROLE_ADMIN])) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can create promotiones.
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
     * Determine whether the user can update the promotion.
     *
     * @param User      $user
     * @param Promotion $promotion
     *
     * @return bool
     */
    public function update(User $user, Promotion $promotion): bool
    {
        return false;
    }

    /**
     * Determine whether the user can delete the promotion.
     *
     * @param User      $user
     * @param Promotion $promotion
     *
     * @return bool
     */
    public function delete(User $user, Promotion $promotion): bool
    {
        return false;
    }
}
