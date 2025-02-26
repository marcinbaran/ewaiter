<?php

namespace App\Enum\Table;

use App\Enum\EnumTrait;

enum TableCreateFormType: int
{
    use EnumTrait;

    case SINGLE = 1;

    case RANGE = 2;
}
