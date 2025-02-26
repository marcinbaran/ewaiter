<?php

namespace App\Http\Filters\Bill;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;
use Rappasoft\LaravelLivewireTables\Views\Filters\MultiSelectFilter;

class PaymentType
{
    public function prepare()
    {
        return
            MultiSelectFilter::make(__('admin.Payment type'))
                ->options(
                    Arr::mapWithKeys(\App\Enum\PaymentType::getValues(), fn ($item) => [$item => __('admin.'.$item)])
                )
                ->filter(function (Builder $builder, array $value) {
                    if (count($value) > 1) {
                        $builder->whereIn('paid_type', $value);
                    } elseif (count($value) === 1) {
                        $builder->where('paid_type', $value[0]);
                    }
                });
    }
}
