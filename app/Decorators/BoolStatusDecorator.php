<?php

namespace App\Decorators;

class BoolStatusDecorator
{
    /**
     * Decorate the boolean value to a checkbox.
     *
     * @param bool $value
     * @return string
     */
    public static function isEdited($isActive)
    {
        return $isActive == 1 ?
            view('admin.partials.decorators.bool-status', ['description' => __('admin.Active'), 'isActive' => true]) :
            view('admin.partials.decorators.bool-status', ['description' => __('admin.Inactive'), 'isActive' => false]);
    }

    public function decorate($isActive)
    {
        return $isActive == 1 ?
            view('admin.partials.decorators.bool-status', ['description' => __('admin.Active'), 'isActive' => true]) :
            view('admin.partials.decorators.bool-status', ['description' => __('admin.Inactive'), 'isActive' => false]);
    }
}
