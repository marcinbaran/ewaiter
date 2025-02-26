<?php

namespace App\Enum;

enum PaymentType: string
{
    use EnumTrait;
    case CASH = 'cash';
    case CARD = 'card_delivery';
    case ELECTRONIC_PAYMENT = 'card';
    case HOTEL_BILL = 'hotel_bill';
    case PRZELEWY24 = 'card_p24';
    case TPAY = 'card_tpay';

}
