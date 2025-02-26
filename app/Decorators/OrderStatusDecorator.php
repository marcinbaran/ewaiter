<?php

namespace App\Decorators;

use App\Models\Bill;
use App\Models\Order;

class OrderStatusDecorator
{
    public function decorate(Bill|Order $bill)
    {
        return view('admin.partials.decorators.order-status-decorator', [
            'label' => gtrans('orders.'.ucfirst($bill->getStatusName($bill->status))),
            'bill' => $bill,
        ]);
    }
}
