<?php

namespace App\Helpers;

use Illuminate\Support\Str;

class MoneyFormatter
{
    public static function format(string|float|int $amount): float
    {
        if (is_string($amount)) {
            $amount = (float) Str::replace(',', '.', trim($amount));
        }

        return (float) number_format(round($amount, 2), 2, '.', '');
    }
}
