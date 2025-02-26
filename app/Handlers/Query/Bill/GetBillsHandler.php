<?php

namespace App\Handlers\Query\Bill;

use App\Queries\Bill\GetBillsQuery;
use App\Repositories\Eloquent\BillRepository;
use Ecotone\Modelling\Attribute\QueryHandler;
use Ecotone\Modelling\QueryBus;

class GetBillsHandler
{
    public function __construct(
        protected QueryBus $queryBus,
        protected BillRepository $billRepository,
    ) {
    }

    #[QueryHandler]
    public function getBills(GetBillsQuery $query)
    {
        return $this->billRepository->getBills();
    }
}
