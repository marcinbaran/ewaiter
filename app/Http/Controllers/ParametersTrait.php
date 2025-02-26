<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

trait ParametersTrait
{
    public function getParams(Request $request, array $keys = null)
    {
        if (null === $keys) {
            return $request->all();
        }
        $params = [];
        foreach ($keys as $ix => $key) {
            $default = null;
            if (is_string($ix)) {
                $default = $key;
                $key = $ix;
            }
            if ($request->has($key) || null !== $default) {
                $params[$key] = $request->has($key) ? $request->$key : $default;
            }
        }

        return $params;
    }
}
