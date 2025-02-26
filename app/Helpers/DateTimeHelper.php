<?php

namespace App\Helpers;

use Carbon\Carbon;

class DateTimeHelper
{
    public static function getDateTimeRangeFromTimeStrings(string $startTimeString, string $endTimeString, string $objectDateString)
    {
        $workTimeEndsNextDay = Carbon::parse($startTimeString)->greaterThan(Carbon::parse($endTimeString));
        $workTimeEndDate = $workTimeEndsNextDay ? Carbon::parse($objectDateString)->addDay()->toDateString() : Carbon::parse($objectDateString)->toDateString();

        $startDateTime = Carbon::createFromTimestamp(strtotime(Carbon::parse($objectDateString)->toDateString().' '.$startTimeString))->format('Y-m-d H:i:s');
        $endDateTime = Carbon::createFromTimestamp(strtotime($workTimeEndDate.' '.$endTimeString))->format('Y-m-d H:i:s');

        return [
            'start' => Carbon::parse($startDateTime),
            'end' => Carbon::parse($endDateTime),
        ];
    }

    public static function isDateExpired(Carbon $expirationDateTime): bool
    {
        return $expirationDateTime->startOfDay() < Carbon::today();
    }
}
