<?php

namespace App\Enum;

enum VisitObject: string
{
    use EnumTrait;

    case RESTAURANT = 'restaurant';
    case DISH = 'dish';
}
