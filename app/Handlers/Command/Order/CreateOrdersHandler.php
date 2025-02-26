<?php

namespace App\Handlers\Command\Order;

use App\Commands\Order\CreateOrdersCommand;
use App\Repositories\Interfaces\OrderRepositoryInterface;
use Ecotone\Modelling\Attribute\CommandHandler;
use Ecotone\Modelling\CommandBus;

class CreateOrdersHandler
{
    public function __construct(
        protected CommandBus $commandBus,
        protected OrderRepositoryInterface $orderRepository,
    ) {
    }

    #[CommandHandler]
    public function createOrder(CreateOrdersCommand $command): void
    {
        $this->orderRepository->createOrdersFromCollection($command->getOrdersCollection());
    }
}
