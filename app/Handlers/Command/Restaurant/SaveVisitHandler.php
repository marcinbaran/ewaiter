<?php

namespace App\Handlers\Command\Restaurant;

use App\Commands\Restaurant\SaveVisitCommand;
use App\Models\Visit;
use Ecotone\Modelling\Attribute\CommandHandler;
use Ecotone\Modelling\CommandBus;

class SaveVisitHandler
{
    public function __construct(
        protected CommandBus $commandBus,
    ) {
    }

    #[CommandHandler]
    public function saveVisit(SaveVisitCommand $command): void
    {
        Visit::create($command->toArray());
    }
}
