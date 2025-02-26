<?php

namespace App\Enum\Commission;

use App\Enum\EnumTrait;

enum CommissionStatus: string
{
    use EnumTrait;

    case ACTIVE = 'active';
    case FINISHED = 'finished';
    case CANCELED = 'canceled';
}
