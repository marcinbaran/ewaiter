<?php

namespace App\Enum;

enum DeliveryMethod: string
{
    use EnumTrait;
    case DELIVERY_TO_ADDRESS = 'delivery_address';
    case TABLE_DELIVERY = 'delivery_table';
    case ROOM_DELIVERY = 'delivery_room';
    case PERSONAL_PICKUP = 'delivery_personal_pickup';
}
