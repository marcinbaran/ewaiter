<?php

namespace App\Handlers\Command\AttributeGroup;

use App\Commands\AttributeGroup\CreateAttributeGroupCommand;
use App\Handlers\Command\CommandHandlerInterface;
use App\Models\AttributeGroup;
use Ecotone\Modelling\Attribute\CommandHandler;
use Ecotone\Modelling\CommandBus;

class CreateAttributeGroupHandler implements CommandHandlerInterface
{
    public function __construct(
        protected CommandBus $commandBus,
    ) {
    }

    #[CommandHandler]
    public function createAttribute(CreateAttributeGroupCommand $command): void
    {
        AttributeGroup::create($command->toArray());
    }
}
