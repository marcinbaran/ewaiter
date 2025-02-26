<?php

namespace App\Policies;

use App\Models\TrWithdrawal;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class TrWithdrawPolicy
{
    use HandlesAuthorization;

    /**
     * before function.
     *
     * @param $user
     *
     * @return bool
     * */
    public function before($user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the order.
     *
     * @param User  $user
     * @param TrWithdrawal $tr_withdraw
     *
     * @return bool
     */
    public function view(User $user, TrWithdrawal $tr_withdraw): bool
    {
        return true;
    }

    /**
     * Determine whether the user can create orders.
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
     * Determine whether the user can update the order.
     *
     * @param User  $user
     * @param TrWithdrawal $tr_withdraw
     *
     * @return bool
     */
    public function update(User $user, TrWithdrawal $tr_withdraw): bool
    {
        return true;
    }

    /**
     * Determine whether the user can delete the order.
     *
     * @param User  $user
     * @param TrWithdrawal $tr_withdraw
     *
     * @return bool
     */
    public function delete(User $user, TrWithdrawal $tr_withdraw): bool
    {
        return true;
    }
}
