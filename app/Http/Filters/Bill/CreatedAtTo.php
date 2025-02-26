<?php

namespace App\Http\Filters\Bill;

use Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\Views\Filters\DateFilter;

class CreatedAtTo
{
    public function prepare()
    {
        return
            DateFilter::make(__('admin.Created at to'), 'created_at_to')
                ->filter(function (Builder $builder, $value) {
                    $builder->where('created_at', '<=', $value);
                });
    }
}
