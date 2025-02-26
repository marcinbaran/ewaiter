<?php

namespace App\Http\Filters\Bill;

use App\Models\Bill;
use Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\Views\Filters\MultiSelectFilter;

class IsPaid
{
    public function prepare()
    {
        return
            MultiSelectFilter::make(__('admin.Is paid'))
                ->options([
                    Bill::PAID_NO => __('admin.NoPaid'),
                    Bill::PAID_YES => __('admin.Paid'),
                ])
                ->filter(function (Builder $builder, array $value) {
                    $builder->whereIn('paid', $value);
                });
    }
}
