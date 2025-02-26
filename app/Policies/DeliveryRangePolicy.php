<?php

namespace App\Policies;

use App\Models\DeliveryRange;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class DeliveryRangePolicy
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
     * Determine whether the user can view the addition.
     *
     * @param User     $user
     * @param DeliveryRange $delivery_range
     *
     * @return bool
     */
    public function view(User $user, DeliveryRange $delivery_range): bool
    {
        if ($user->isOne([User::ROLE_MANAGER, User::ROLE_ADMIN])) {
            return true;
        }
    }

    /**
     * Determine whether the user can create delivery_ranges.
     *
     * @param User $user
     *
     * @return bool
     */
    public function create(User $user): bool
    {
        // if ($user->isOne([User::ROLE_MANAGER, User::ROLE_ADMIN])) {
        //     $range_db = DeliveryRange::where('out_of_range', true)->first();
        //     return !$range_db ? true : false;
        // }
        return true;
    }

    /**
     * Determine whether the user can update the addition.
     *
     * @param User     $user
     * @param DeliveryRange $delivery_range
     *
     * @return bool
     */
    public function update(User $user, DeliveryRange $delivery_range): bool
    {
        return false;
    }

    /**
     * Determine whether the user can delete the addition.
     *
     * @param User     $user
     * @param DeliveryRange $delivery_range
     *
     * @return bool
     */
    public function delete(User $user, DeliveryRange $delivery_range): bool
    {
        if ($user->isOne([User::ROLE_MANAGER, User::ROLE_ADMIN])) {
            $range_db = DeliveryRange::orderBy('range_to', 'desc')->first();

            return $range_db->id == $delivery_range->id ? true : false;
        }
    }
}
