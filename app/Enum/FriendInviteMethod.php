<?php

namespace App\Enum;

enum FriendInviteMethod :string
{
    case EMAIL = 'email';
    case PHONE = 'phone';
    case QR_CODE = 'qr_code';
}
