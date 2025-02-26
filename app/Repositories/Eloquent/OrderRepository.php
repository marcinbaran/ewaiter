<?php

namespace App\Repositories\Eloquent;

use App\Models\Order;
use App\Repositories\Interfaces\OrderRepositoryInterface;
use Illuminate\Support\Collection;

class OrderRepository implements OrderRepositoryInterface
{
    public function createOrdersFromCollection(Collection $orders): void
    {
        foreach ($orders as $order) {
            Order::create($order->toArray());
        }
    }

    public function getOrders(array $filters = []): ?array
    {
        $queryBuilder = Order::query();

        foreach ($filters as $filter) {
            $queryBuilder->where($filter['column'], $filter['operator'], $filter['value']);
        }

        return $queryBuilder->get()->toArray();
    }

    public function getSingleOrder(int $orderId): ?Order
    {
        return Order::findOrFail($orderId)->first();
    }
}
