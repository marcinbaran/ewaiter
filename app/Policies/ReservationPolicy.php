<?php

namespace App\Policies;

use App\Models\Reservation;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ReservationPolicy
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
        if ($user->isOne([User::ROLE_MANAGER, User::ROLE_ADMIN, User::ROLE_WAITER])) {
            return true;
        }
    }

    /**
     * Determine whether the user can view the addition.
     *
     * @param $user
     * @param Reservation $reservation
     *
     * @return bool
     */
    public function view($user, Reservation $reservation): bool
    {
        return true;
    }

    /**
     * Determine whether the user can create reservations.
     *
     * @param $user
     *
     * @return bool
     */
    public function create($user): bool
    {
        return false;
    }

    /**
     * Determine whether the user can update the addition.
     *
     * @param $user
     * @param Reservation $reservation
     *
     * @return bool
     */
    public function update($user, Reservation $reservation): bool
    {
        return false;
    }

    /**
     * Determine whether the user can delete the addition.
     *
     * @param $user
     * @param Reservation $reservation
     *
     * @return bool
     */
    public function delete($user, Reservation $reservation): bool
    {
        return false;
    }
}
