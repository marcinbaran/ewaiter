<?php

namespace App\Handlers\Command\Attribute;

use App\Commands\Attribute\DeleteAttributeCommand;
use App\Handlers\Command\CommandHandlerInterface;
use App\Models\Attribute;
use Ecotone\Modelling\Attribute\CommandHandler;
use Ecotone\Modelling\CommandBus;

class DeleteAttributeHandler implements CommandHandlerInterface
{
    public function __construct(
        protected CommandBus $commandBus,
    ) {
    }

    #[CommandHandler]
    public function deleteAttribute(DeleteAttributeCommand $command): void
    {
        Attribute::findOrFail($command->getAttributeId())->delete();
    }
}
