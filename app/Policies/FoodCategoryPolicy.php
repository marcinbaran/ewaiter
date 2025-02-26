<?php

namespace App\Policies;

use App\Models\FoodCategory;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class FoodCategoryPolicy
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
     * Determine whether the user can view the food category.
     *
     * @param User         $user
     * @param FoodCategory $foodCategory
     *
     * @return bool
     */
    public function view(User $user, FoodCategory $foodCategory): bool
    {
        return true;
    }

    /**
     * Determine whether the user can create food categories.
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
     * Determine whether the user can update the food category.
     *
     * @param User         $user
     * @param FoodCategory $foodCategory
     *
     * @return bool
     */
    public function update(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can delete the food category.
     *
     * @param User         $user
     * @param FoodCategory $foodCategory
     *
     * @return bool
     */
    public function delete(User $user, FoodCategory $foodCategory): bool
    {
        if ($user->isOne([User::ROLE_MANAGER, User::ROLE_ADMIN])) {
            return true;
        } else {
            return false;
        }
    }
}
