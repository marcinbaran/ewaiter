<?php

namespace App\Commands\Address;

use App\Commands\CommandInterface;
use App\DTO\Orders\CreateAddressDTO;

class CreateAddressCommand implements CommandInterface
{
    public function __construct(
        protected CreateAddressDTO $addressDTO,
    ) {
    }

    public function toArray(): array
    {
        return $this->addressDTO->toArray();
    }

    public function setId($id): void
    {
        $this->addressDTO->setId($id);
    }
}
