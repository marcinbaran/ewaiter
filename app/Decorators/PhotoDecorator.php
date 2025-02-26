<?php

namespace App\Decorators;

use Illuminate\View\View;

class PhotoDecorator
{
    public function decorate(string $photoUrl, string $photoAlt): View
    {
        return view('admin.partials.decorators.photo-decorator', [
            'photoUrl' => $photoUrl,
            'photoAlt' => $photoAlt,
        ]);
    }
}
