<?php

namespace App\Handlers\Command\Attribute;

use App\Commands\Attribute\CreateAttributeCommand;
use App\Handlers\Command\CommandHandlerInterface;
use App\Models\Attribute;
use Ecotone\Modelling\Attribute\CommandHandler;
use Ecotone\Modelling\CommandBus;

class CreateAttributeHandler implements CommandHandlerInterface
{
    public function __construct(
        protected CommandBus $commandBus,
    ) {
    }

    #[CommandHandler]
    public function createAttribute(CreateAttributeCommand $command): void
    {
        Attribute::create($command->toArray());
    }
}
