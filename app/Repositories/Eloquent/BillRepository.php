<?php

namespace App\Repositories\Eloquent;

use App\Models\Bill;
use App\Models\UserSystem;
use App\Repositories\Interfaces\BillRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class BillRepository implements BillRepositoryInterface
{
    public function createBill(array $billData): Bill
    {
        return Bill::create($billData);
    }

    public function getBills(array $filters = []): ?array
    {
        $queryBuilder = Bill::query();

        foreach ($filters as $filter) {
            $queryBuilder->where($filter['column'], $filter['operator'], $filter['value']);
        }

        return $queryBuilder->get()->toArray();
    }

    public function getSingleBill(int $billId): ?Bill
    {
        return Bill::findOrFail($billId)->first();
    }

    public function getBillsWithStatus(array $status, bool $withOrders = true, bool $withAddress = true, bool $withUser = true, bool $withUpdatedAt = true): ?Collection
    {
        $orders = Bill::query()->placed()->orderBy('created_at')->whereIn('status', $status)->get();

        return $orders->each(function ($order) use ($withOrders, $withAddress, $withUser, $withUpdatedAt) {
            $order->with_orders = $withOrders;
            $order->with_address = $withAddress;
            $order->with_user = $withUser;
            $order->with_updated_at = $withUpdatedAt;

            if (isset($order->user) && in_array(UserSystem::ROLE_GUEST, $order->user->roles)) {
                $order->user->phone = $order?->phone ?? null;
                $order->user->email = $order?->email ?? null;
                $order->user->first_name = trans('admin.not_logged_in');
            }
        });
//            ->filter(function ($order) {
//            return isset($order->user) && $order->user->first_name !== 'deleted_user';
//        });
    }
}
