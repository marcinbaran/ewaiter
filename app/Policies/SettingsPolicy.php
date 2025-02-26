<?php

namespace App\Policies;

use App\Models\Settings;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class SettingsPolicy
{
    use HandlesAuthorization;

    public function before($user)
    {
    }

    public function view(User $user, Settings $model): bool
    {
        return $user->isOne([User::ROLE_ADMIN, User::ROLE_MANAGER]);
    }

    public function create(User $user): bool
    {
        if ($user->email == 'aplikacje@zetorzeszow.pl') {
            return true;
        } else {
            return false;
        }
    }

    public function edit(User $user): bool
    {
        return $user->isOne([User::ROLE_ADMIN, User::ROLE_MANAGER]);
    }

    public function update(User $user, Settings $model): bool
    {
        return $user->isOne([User::ROLE_ADMIN, User::ROLE_MANAGER]);
    }

    public function delete(User $user, Settings $model): bool
    {
        return false;
        //return $user->isOne([User::ROLE_ADMIN,User::ROLE_MANAGER]);
    }
}
