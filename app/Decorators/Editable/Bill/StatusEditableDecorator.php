<?php

namespace App\Decorators\Editable\Bill;

use App\Decorators\EditableColumnDecorator;
use App\Decorators\OrderStatusDecorator;
use App\Models\Bill;

class StatusEditableDecorator extends EditableColumnDecorator
{
    public static function create(Bill $row, $value)
    {
        $options = [];
        foreach ($row->getPossibleNextStatuses() as $value => $label) {
            $options[] = [
                'label' => $label,
                'value' => $value,
            ];
        }

        $decorator = new self();
        $decorator
            ->setColumn('status')
            ->setLabel((new OrderStatusDecorator())->decorate($row))
            ->setModel($row)
            ->setCurrentValue($value)
            ->setOptions($options)
            ->setType('select');

        return $decorator;
    }
}
