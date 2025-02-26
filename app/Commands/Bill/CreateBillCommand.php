<?php

namespace App\Commands\Bill;

use App\Commands\CommandInterface;
use App\DTO\Orders\CreateBillDTO;

class CreateBillCommand implements CommandInterface
{
    public function __construct(
        protected CreateBillDTO $billDTO,
    ) {
    }

    public function getBillDto(): CreateBillDTO
    {
        return $this->billDTO;
    }

    public function setId($id): void
    {
        $this->billDTO->setId($id);
    }
}
