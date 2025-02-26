<?php

namespace App\Decorators\Editable\Bill;

use App\Decorators\BoolStatusDecorator;
use App\Decorators\EditableColumnDecorator;
use App\Models\Bill;

class PaidEditableDecorator extends EditableColumnDecorator
{
    public static function create(Bill $row, $value)
    {
        $options = [
            [
                'label' => __('admin.No'),
                'value' => 0,
            ],
            [
                'label' => __('admin.Yes'),
                'value' => 1,
            ],
        ];

        $decorator = new self();
        $decorator
            ->setColumn('paid')
            ->setLabel((new BoolStatusDecorator())->decorate($row->paid))
            ->setModel($row)
            ->setCurrentValue($value)
            ->setOptions($options)
            ->setType('select');

        return $decorator;
    }
}
