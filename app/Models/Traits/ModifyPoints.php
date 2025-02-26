<?php

namespace App\Models\Traits;

use App\Facades\ReferringUserService;
use App\Models\User;
use App\Models\UserSystem;

trait ModifyPoints
{
    public function modify_points($amount): bool
    {
        if (! $this->isAllowedType()) {
            throw new \Exception('Trait ModifyPoints can be implemented only in the User and UserSystem model');
        }

        try {
            return ReferringUserService::modifyBalanceForReferringUser(ReferringUserService::getReferringUser($this), $amount);
        } catch (\Exception $e) {
            return 0.0;
        }
    }

    protected function isAllowedType(): bool
    {
        return in_array(get_class($this), [User::class, UserSystem::class]);
    }
}
