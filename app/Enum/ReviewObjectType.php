<?php

namespace App\Enum;

enum ReviewObjectType: string
{
    use EnumTrait;
    case DELIVERY = 'delivery';
    case FOOD = 'food';
}
