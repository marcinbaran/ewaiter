<?php

namespace App\Models;

use App\Services\UtilService;

trait ModelTrait
{
    public static function decamelize($param)
    {
        return strtolower(preg_replace(['/([a-z\d])([A-Z])/', '/([^_])([A-Z][a-z])/'], '$1_$2', $param));
    }

    public function update(array $attributes = [], array $options = [])
    {
        return parent::update(self::decamelizeArray($attributes), $options);
    }

    public static function decamelizeArray(array $attributes)
    {
        $keys = array_keys($attributes);
        foreach ($keys as $key => &$val) {
            $val = self::decamelize($val);
        }

        return array_combine($keys, array_values($attributes));
    }

    public static function getLocales()
    {
        return UtilService::getLocales();
    }
}
