<?php

namespace App\Policies;

use App\Models\Commission;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class CommissionPolicy
{
    use HandlesAuthorization;

    public function before($user): bool
    {
        return true;
    }

    public function view(User $user, Commission $commission): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, Commission $commission): bool
    {
        return true;
    }

    public function delete(User $user, Commission $commission): bool
    {
        return true;
    }
}
