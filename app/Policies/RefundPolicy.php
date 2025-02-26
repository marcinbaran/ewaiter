<?php

namespace App\Policies;

use App\Models\Refund;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class RefundPolicy
{
    use HandlesAuthorization;

    /**
     * before function.
     *
     * @param $user
     *
     * */
    public function before($user)
    {
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param User    $user
     * @param Refund $model
     *
     * @return bool
     */
    public function view(User $user, Refund $model): bool
    {
        return true;
    }

    /**
     * Determine whether the user can create models.
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
     * Determine whether the user can update the model.
     *
     * @param User    $user
     * @param Refund $model
     *
     * @return bool
     */
    public function update(User $user, Refund $model): bool
    {
        return false;
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param User    $user
     * @param Refund $model
     *
     * @return bool
     */
    public function delete(User $user, Refund $model): bool
    {
        return false;
    }
}
