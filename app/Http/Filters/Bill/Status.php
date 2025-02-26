<?php

namespace App\Http\Filters\Bill;

use App\Models\Bill;
use Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\Views\Filters\MultiSelectFilter;

class Status
{
    public function prepare()
    {
        $bill = new Bill();

        return
            MultiSelectFilter::make('Status')
                ->options([
                    Bill::STATUS_NEW => gtrans('orders.'.ucfirst($bill->getStatusName(Bill::STATUS_NEW))),
                    Bill::STATUS_ACCEPTED => gtrans('orders.'.ucfirst($bill->getStatusName(Bill::STATUS_ACCEPTED))),
                    Bill::STATUS_READY => gtrans('orders.'.ucfirst($bill->getStatusName(Bill::STATUS_READY))),
                    Bill::STATUS_RELEASED => gtrans('orders.'.ucfirst($bill->getStatusName(Bill::STATUS_RELEASED))),
                    Bill::STATUS_CANCELED => gtrans('orders.'.ucfirst($bill->getStatusName(Bill::STATUS_CANCELED))),
                    Bill::STATUS_COMPLAINT => gtrans('orders.'.ucfirst($bill->getStatusName(Bill::STATUS_COMPLAINT))),
                ])
                ->filter(function (Builder $builder, array $value) {
                    $builder->whereIn('status', $value);
                });
    }
}
