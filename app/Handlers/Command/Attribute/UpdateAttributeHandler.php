<?php

namespace App\Handlers\Command\Attribute;

use App\Commands\Attribute\UpdateAttributeCommand;
use App\Handlers\Command\CommandHandlerInterface;
use App\Models\Attribute;
use Ecotone\Modelling\Attribute\CommandHandler;
use Ecotone\Modelling\CommandBus;

class UpdateAttributeHandler implements CommandHandlerInterface
{
    public function __construct(
        protected CommandBus $commandBus,
    ) {
    }

    #[CommandHandler]
    public function updateAttribute(UpdateAttributeCommand $command): void
    {
        Attribute::findOrFail($command->getAttributeId())->update($command->toArray());
    }
}
