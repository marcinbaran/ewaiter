<?php

namespace App\Policies;

use App\Models\Dish;
use App\Models\UserSystem;
use Illuminate\Auth\Access\HandlesAuthorization;

class DishPolicy
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
        return true;
    }

    /**
     * Determine whether the user can view the dish.
     *
     * @param UserSystem $user
     * @param Dish $dish
     *
     * @return bool
     */
    public function view(Dish $dish): bool
    {
        return true;
    }

    /**
     * Determine whether the user can create dishes.
     *
     * @param UserSystem $user
     *
     * @return bool
     */
    public function create(UserSystem $user): bool
    {
        return false;
    }

    /**
     * Determine whether the user can update the dish.
     *
     * @param UserSystem $user
     * @param Dish $dish
     *
     * @return bool
     */
    public function update(UserSystem $user, Dish $dish): bool
    {
        return false;
    }

    /**
     * Determine whether the user can delete the dish.
     *
     * @param UserSystem $user
     * @param Dish $dish
     *
     * @return bool
     */
    public function delete(UserSystem $user, Dish $dish): bool
    {
        return false;
    }
}
