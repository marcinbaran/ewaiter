<?php

namespace App\Policies;

use App\Models\Bill;
use App\Models\Order;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class BillPolicy
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
     * Determine whether the user can view the bill.
     *
     * @param User $user
     * @param Bill $bill
     *
     * @return bool
     */
    public function view(User $user, Bill $bill): bool
    {
        return true;
    }

    /**
     * Determine whether the user can create bills.
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
     * Determine whether the user can update the bill.
     *
     * @param User $user
     * @param Bill $bill
     *
     * @return bool
     */
    public function update(User $user, Bill $bill): bool
    {
        return false;
    }

    /**
     * Determine whether the user can delete the bill.
     *
     * @param User $user
     * @param Bill $bill
     *
     * @return bool
     */
    public function delete(User $user, Bill $bill): bool
    {
        return $user->isOne([User::ROLE_ADMIN, User::ROLE_MANAGER]) && ! $bill->orders()->where('paid', Order::PAID_YES)->count();
    }
}
