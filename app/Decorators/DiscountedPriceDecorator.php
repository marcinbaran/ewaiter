<?php

namespace App\Decorators;

class DiscountedPriceDecorator
{
    public function decorate(float $price, float $discountedPrice, string $currency = 'PLN')
    {
        return view(
            'admin.partials.decorators.discounted-price',
            ['price' => $price, 'discountedPrice' => $discountedPrice, 'currency' => $currency]
        );
    }
}
