<?php

namespace App\Policies;

use App\Models\Order;
use App\Models\Table;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class TablePolicy
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
     * Determine whether the user can view the table.
     *
     * @param User  $user
     * @param Table $table
     *
     * @return bool
     */
    public function view(User $user, Table $table): bool
    {
        return true;
    }

    /**
     * Determine whether the user can create tablees.
     *
     * @param User $user
     *
     * @return bool
     */
    public function create(User $user): bool
    {
        return $user->isOne([User::ROLE_MANAGER, User::ROLE_ADMIN]);
    }

    /**
     * Determine whether the user can update the table.
     *
     * @param User  $user
     * @param Table $table
     *
     * @return bool
     */
    public function update(User $user, Table $table): bool
    {
        return $user->isOne([User::ROLE_MANAGER, User::ROLE_ADMIN]);
    }

    /**
     * Determine whether the user can delete the table.
     *
     * @param User  $user
     * @param Table $table
     *
     * @return bool
     */
    public function delete(User $user, Table $table): bool
    {
        return $user->isOne([User::ROLE_ADMIN, User::ROLE_MANAGER]) && ! $table->orders()
            ->where('paid', Order::PAID_NO)
            ->whereBetween('status', [Order::STATUS_ACCEPTED, Order::STATUS_READY])
            ->count();
    }
}
