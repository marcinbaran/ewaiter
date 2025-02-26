<?php

namespace App\Enum\Promotion;

enum PromotionValidationFailType: int
{
    case PROMOTION_DATE_INVALID = 0;
    case PROMOTION_EXISTS = 1;
    case PROMOTION_DATE_INVALID_AND_EXISTS = 2;
    case PROMOTION_NAME_EXISTS = 3;
    case PROMOTION_OVERVALUED = 4;
}
