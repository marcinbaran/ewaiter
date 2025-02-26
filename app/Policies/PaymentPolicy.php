<?php

namespace App\Policies;

use App\Models\Payment;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class PaymentPolicy
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
//        if ($user->isOne([User::ROLE_ADMIN])) {
//        }
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param $user
     * @param Payment $model
     *
     * @return bool
     */
    public function view($user, Payment $model): bool
    {
        return true;
    }

    /**
     * Determine whether the user can create models.
     *
     * @param $user
     *
     * @return bool
     */
    public function create($user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param $user
     * @param Payment $model
     *
     * @return bool
     */
    public function update($user, Payment $model): bool
    {
        return false;
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param $user
     * @param Payment $model
     *
     * @return bool
     */
    public function delete($user, Payment $model): bool
    {
        return false;
    }
}
