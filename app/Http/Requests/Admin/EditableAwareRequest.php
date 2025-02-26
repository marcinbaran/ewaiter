<?php

namespace App\Http\Requests\Admin;

trait EditableAwareRequest
{
    public function getUpdateRules()
    {
        foreach (self::$rules as $route => $rules) {
            if (strpos($route, 'update') !== false) {
                return $rules;
            }
        }

        return [];
    }
}
