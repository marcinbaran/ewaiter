<?php

namespace App\Queries\Bill;

use App\Queries\QueryBase;

class GetBillsQuery extends QueryBase
{
    public function __construct(
        public array $filters = []
    ) {}

}
