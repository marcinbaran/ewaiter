<?php

namespace App\Handlers\Command\AttributeGroup;

use App\Commands\AttributeGroup\UpdateAttributeGroupCommand;
use App\Handlers\Command\CommandHandlerInterface;
use App\Models\AttributeGroup;
use Ecotone\Modelling\Attribute\CommandHandler;
use Ecotone\Modelling\CommandBus;

class UpdateAttributeGroupHandler implements CommandHandlerInterface
{
    public function __construct(
        protected CommandBus $commandBus,
    ) {
    }

    #[CommandHandler]
    public function updateAttributeGroup(UpdateAttributeGroupCommand $command): void
    {
        AttributeGroup::findOrFail($command->getAttributeGroupId())->update($command->toArray());
    }
}
