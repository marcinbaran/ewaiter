<?php

namespace App\Policies;

use App\Models\Order;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class OrderPolicy
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
     * @param Order $order
     *
     * @return bool
     */
    public function view(User $user, Order $order): bool
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
     * @param Order $order
     *
     * @return bool
     */
    public function update(User $user, Order $order): bool
    {
        return true;
    }

    /**
     * Determine whether the user can delete the order.
     *
     * @param User  $user
     * @param Order $order
     *
     * @return bool
     */
    public function delete(User $user, Order $order): bool
    {
        return (Order::STATUS_NEW == $order->status || $user->isOne([User::ROLE_ADMIN, User::ROLE_MANAGER])) && Order::PAID_YES !== $order->paid;
    }
}
