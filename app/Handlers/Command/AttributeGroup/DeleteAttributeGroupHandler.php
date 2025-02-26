<?php

namespace App\Handlers\Command\AttributeGroup;

use App\Commands\AttributeGroup\DeleteAttributeGroupCommand;
use App\Handlers\Command\CommandHandlerInterface;
use App\Models\AttributeGroup;
use Ecotone\Modelling\Attribute\CommandHandler;
use Ecotone\Modelling\CommandBus;

class DeleteAttributeGroupHandler implements CommandHandlerInterface
{
    public function __construct(
        protected CommandBus $commandBus,
    ) {
    }

    #[CommandHandler]
    public function deleteAttributeGroup(DeleteAttributeGroupCommand $command): void
    {
        AttributeGroup::findOrFail($command->getAttributeGroupId())->delete();
    }
}
