<?php

namespace App\Http\Requests;

trait RequestTrait
{
    public static function getRule(string $key = null)
    {
        return $key ? self::$rules[$key] : self::$rules;
    }
}
