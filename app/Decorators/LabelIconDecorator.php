<?php

namespace App\Decorators;

class LabelIconDecorator
{
    public function decorate($value)
    {
        return view('admin.partials.decorators.label-icon-decorator', [
            'icon' => $value,
        ]);
    }
}
