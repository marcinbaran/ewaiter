<?php

namespace App\Enum;

enum ReservationStatus: int
{
    case PENDING = 0;
    case CONFIRMED = 1;
    case CANCELLED = 2;
}
