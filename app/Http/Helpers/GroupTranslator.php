<?php

namespace App\Http\Helpers;

class GroupTranslator
{
    /**
     * @param string $key
     * @param array  $replace
     * @param string $locale
     *
     * @return string
     */
    public static function group_trans(string $key, array $replace = [], string $locale = null): string
    {
        return app('translator')->hasForLocale($key, $locale) ? app('translator')->get($key, $replace, $locale) : substr(strstr($key, '.'), 1);
    }
}
