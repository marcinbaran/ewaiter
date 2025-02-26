<?php

namespace App\Enum;

enum ExternalLoginType: string
{
    case GOOGLE = 'google';
    case APPLE = 'apple';
    case FACEBOOK = 'facebook';
}
