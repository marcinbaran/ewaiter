<?php

namespace App\Enum;

enum PromotionType: int
{
    case DISH = 0;
    case BILL = 1;
    case CATEGORY = 2;
    case BUNDLE = 3;
    case MERGE = 4;
}
