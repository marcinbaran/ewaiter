<?php

namespace App\Handlers\Command\Address;

use App\Commands\Address\CreateAddressCommand;
use App\Repositories\Interfaces\AddressRepositoryInterface;
use Ecotone\Modelling\Attribute\CommandHandler;
use Ecotone\Modelling\CommandBus;

class CreateAddressHandler
{
    public function __construct(
        protected CommandBus $commandBus,
        protected AddressRepositoryInterface $addressRepository
    ) {
    }

    #[CommandHandler]
    public function createAddress(CreateAddressCommand $command): void
    {
        $createdAddress = $this->addressRepository->createAddress($command->toArray());
        $command->setId($createdAddress->id);
    }
}
