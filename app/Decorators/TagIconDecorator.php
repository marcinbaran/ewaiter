<?php

namespace App\Decorators;

class TagIconDecorator
{
    public function decorate($value)
    {
        return view('admin.partials.decorators.tag-icon-decorator', [
            'icon' => $value,
        ]);
    }
}
