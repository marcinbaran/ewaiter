<?php

namespace App\Enum\AdditionGroup;

use App\Enum\EnumTrait;

enum AdditionGroupType: int
{
    use EnumTrait;

    case SINGLE = 0;
    case MULTIPLE = 1;
}
