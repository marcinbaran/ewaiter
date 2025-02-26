<?php

namespace App\Decorators\Editable\Commission;

use App\Decorators\EditableColumnDecorator;
use App\Models\Commission;

class CommentEditableDecorator extends EditableColumnDecorator
{
    public static function create(Commission $row, $value): self
    {
        $decorator = new self();

        $validationRules = [
            'minLength' => 3,
            'maxLength' => 255,
            'required' => 'true',
        ];

        $decorator
            ->setColumn('comment')
            ->setLabel($value ?? '-')
            ->setModel($row)
            ->setCurrentValue($value)
            ->setValidationRules($validationRules)
            ->setType('text');

        return $decorator;
    }
}
