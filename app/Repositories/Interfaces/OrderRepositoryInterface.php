<?php

namespace App\Repositories\Interfaces;

use App\Models\Order;
use Illuminate\Support\Collection;

interface OrderRepositoryInterface
{
    public function createOrdersFromCollection(Collection $orders): void;
    public function getOrders(array $filters = []): ?array;
    public function getSingleOrder(int $orderId): ?Order;
}
