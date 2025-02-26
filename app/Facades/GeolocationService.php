<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

class GeolocationService extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'GeolocationService';
    }
}
