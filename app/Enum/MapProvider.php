<?php

namespace App\Enum;

enum MapProvider: string
{
    case GOOGLE = 'google';
    case OPEN_STREET_MAP = 'osm';
}
