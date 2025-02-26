<?php

namespace App\Decorators;

class MoneyDecorator
{
    public function decorate(float $amount, string $currency = 'PLN')
    {
        $prefix = '';
        $suffix = $currency;
        $formattedAmount = number_format($amount, 2, ',', ' ');

        return sprintf('%s%s %s', $prefix, $formattedAmount, $suffix);
    }
}
