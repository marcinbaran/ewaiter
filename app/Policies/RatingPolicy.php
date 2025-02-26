<?php

namespace App\Policies;

use App\Models\Rating;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class RatingPolicy
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
     * Determine whether the user can view the addition.
     *
     * @param User     $user
     * @param Rating $rating
     *
     * @return bool
     */
    public function view(User $user, Rating $rating): bool
    {
        return true;
    }

    /**
     * Determine whether the user can create ratings.
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
     * Determine whether the user can update the addition.
     *
     * @param User     $user
     * @param Rating $rating
     *
     * @return bool
     */
    public function update(User $user, Rating $rating): bool
    {
        return false;
    }

    /**
     * Determine whether the user can delete the addition.
     *
     * @param User     $user
     * @param Rating $rating
     *
     * @return bool
     */
    public function delete(User $user, Rating $rating): bool
    {
        return false;
    }
}
