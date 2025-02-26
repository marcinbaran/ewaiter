<?php

namespace App\Decorators\Editable\Commission;

use App\Decorators\CommissionStatusDecorator;
use App\Decorators\EditableColumnDecorator;
use App\Models\Commission;

class StatusEditableDecorator extends EditableColumnDecorator
{
    public static function create(Commission $row, $value): self
    {
        $options = [
            [
                'label' => __('commissions.statuses.active'),
                'value' => 'active',
            ],
            [
                'label' => __('commissions.statuses.finished'),
                'value' => 'finished',
            ],
            [
                'label' => __('commissions.statuses.canceled'),
                'value' => 'canceled',
            ],
        ];

        $decorator = new self();
        $decorator
            ->setColumn('status')
            ->setLabel((new CommissionStatusDecorator())->decorate($row))
            ->setModel($row)
            ->setCurrentValue($value)
            ->setOptions($options)
            ->setType('select');

        return $decorator;
    }
}
