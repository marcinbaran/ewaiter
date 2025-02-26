<?php

namespace App\Decorators\Editable\Bill;

use App\Decorators\EditableColumnDecorator;
use App\Models\Bill;
use Carbon\Carbon;

class TimeWaitEditableDecorator extends EditableColumnDecorator
{
    public static function create(Bill $row, $value)
    {
        $decorator = new self();
        $decorator
            ->setColumn('time_wait')
            ->setLabel(Carbon::parse($row->time_wait)->format('H:i'))
            ->setModel($row)
            ->setCurrentValue(Carbon::parse($row->time_wait)->format('H:i'))
            ->setType('time');

        return $decorator;
    }
}
