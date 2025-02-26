<?php

namespace App\Handlers\Command\Review;

use App\Commands\Review\DeleteReviewCommand;
use App\Handlers\Command\CommandHandlerInterface;
use App\Models\Review;
use Ecotone\Modelling\Attribute\CommandHandler;
use Ecotone\Modelling\CommandBus;

class DeleteReviewHandler implements CommandHandlerInterface
{
    public function __construct(
        protected CommandBus $commandBus,
    ) {
    }

    #[CommandHandler]
    public function deleteReview(DeleteReviewCommand $command): void
    {
        Review::findOrFail($command->getReviewId())->delete();
    }
}
