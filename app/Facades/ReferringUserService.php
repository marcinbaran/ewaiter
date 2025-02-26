<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

class ReferringUserService extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'ReferringUserService';
    }
}
