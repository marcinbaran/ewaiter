<?php

namespace App\Commands\Order;

use App\Commands\CommandInterface;
use Illuminate\Support\Collection;

class CreateOrdersCommand implements CommandInterface
{
    public function __construct(
        public Collection $orders
    ) {
    }

    public function getOrdersCollection(): Collection
    {
        return $this->orders;
    }
}
