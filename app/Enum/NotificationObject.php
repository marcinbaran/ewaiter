<?php

namespace App\Enum;

enum NotificationObject: string
{
    case NULL = 'null';
    case BILLS = 'bills';
    case MOBILE_TOPICS = 'mobile_topics';
    case ORDERS = 'orders';
    case RESERVATIONS = 'reservations';
}
