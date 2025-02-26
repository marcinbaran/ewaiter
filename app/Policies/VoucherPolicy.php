<?php

namespace App\Policies;

use App\Models\User;

class VoucherPolicy
{
    public function before(User $user)
    {
        if ($user->isOne([User::ROLE_MANAGER, User::ROLE_ADMIN])) {
            return true;
        }

        return false;
    }

    public function view(User $user): bool
    {
        return false;
    }

    public function create(User $user): bool
    {
        return false;
    }

    public function store(User $user): bool
    {
        return false;
    }

    public function edit(User $user): bool
    {
        return false;
    }

    public function update(User $user): bool
    {
        return false;
    }

    public function delete(User $user): bool
    {
        return false;
    }
}
