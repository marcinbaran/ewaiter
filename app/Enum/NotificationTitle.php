<?php

namespace App\Enum;

enum NotificationTitle: string
{
    case ALERT = 'alert';
    case RESERVATION = 'reservation';
    case RESERVATION_MOBILE = 'reservation_mobile';
    case STATUS_BILL = 'status_bill';
    case STATUS_BILL_MOBILE = 'status_bill_mobile';
    case STATUS_ORDER = 'status_order';
    case WAITER = 'waiter';
    case REFUND_MOBILE = 'refund_mobile';
}
