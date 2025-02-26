<?php

namespace App\Http\Filters\Bill;

use App\Enum\DeliveryMethod;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;
use Rappasoft\LaravelLivewireTables\Views\Filters\MultiSelectFilter;

class DeliveryType
{
    public function prepare()
    {
        return
            MultiSelectFilter::make(__('orders.Type of delivery'))
                ->options(
                    Arr::mapWithKeys(DeliveryMethod::getValues(), fn ($item) => [$item => __('admin.'.$item)])
                )
                ->filter(function (Builder $builder, array $value) {
                    if (count($value) > 1) {
                        $builder->whereIn('delivery_type', $value);
                    } elseif (count($value) === 1) {
                        $builder->where('delivery_type', $value[0]);
                    }
                });
    }
}
