<?php

namespace App\Enum;

enum OrderStatus: int
{
    case NEW = 0;
    case ACCEPTED = 1;
    case READY = 2;
    case RELEASED = 3;
    case CANCELED = 4;
    case COMPLAINT = 5;
}
