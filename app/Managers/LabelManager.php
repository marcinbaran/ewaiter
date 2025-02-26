<?php

namespace App\Managers;

use App\Http\Controllers\ParametersTrait;
use App\Models\Label;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LabelManager
{
    use ParametersTrait;

    public function create(Request $request): Label
    {
        $params = $this->getParams($request, ['name', 'icon']);
        $label = DB::connection('tenant')->transaction(function () use ($params) {
            $label = Label::create(Label::decamelizeArray($params))->fresh();

            return $label;
        });

        return $label;
    }

    public function update(Request $request, Label $label): Label
    {
        $params = $this->getParams($request, ['name', 'icon']);
        DB::connection('tenant')->transaction(function () use ($params, $label) {
            if (! empty($params)) {
                $label->update($params);
            }
        });

        return $label;
    }

    public function delete(Label $label): Label
    {
        DB::connection('tenant')->transaction(function () use ($label) {
            $label->delete();
        });

        return $label;
    }
}
