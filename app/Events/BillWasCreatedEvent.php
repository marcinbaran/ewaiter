<?php

namespace App\Events;

use App\Models\Bill;

class BillWasCreatedEvent
{
    public function __construct(
        public Bill $bill
    ) {}

    public function getBillId(): int
    {
        return $this->bill->id;
    }
}
