<?php

namespace App\Decorators;

use Carbon\Carbon;

class DatePeriodDecorator
{
    public function decorate(Carbon $start, Carbon $end, string $format = 'Y-m-d', string $connector = ' - '): string
    {
        return $start->format($format).$connector.$end->format($format);
    }
}
