<?php

namespace App\Enum\Dashboard;

use App\Enum\BaseEnum;

enum ReportDuration: string
{
    case THIS_WEEK = '0';
    case PREVIOUS_WEEK = '1';
    case THIS_MONTH = '2';
    case PREVIOUS_MONTH = '3';
}
