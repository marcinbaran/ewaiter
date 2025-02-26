<?php

namespace App\Repositories\Interfaces;

use App\Models\Bill;

interface BillRepositoryInterface
{
    public function createBill(array $billData): Bill;
    public function getBills(array $filters = []): ?array;
    public function getSingleBill(int $billId): ?Bill;
}
