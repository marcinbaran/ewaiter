<?php

namespace App\Enum\Voucher;

use App\Enum\EnumTrait;

enum VoucherAddingType: int
{
    use EnumTrait;

    case SINGLE = 0;

    case MULTIPLE = 1;
}
