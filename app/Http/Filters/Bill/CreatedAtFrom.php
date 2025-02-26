<?php

namespace App\Http\Filters\Bill;

use Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\Views\Filters\DateFilter;

class CreatedAtFrom
{
    public function prepare()
    {
        return
            DateFilter::make(__('admin.Created at from'), 'created_at')
                ->filter(function (Builder $builder, $value) {
                    $builder->where('created_at', '>=', $value);
                });
    }
}
