<?php

namespace App\Handlers\Command\Bill;

use App\Commands\Bill\CreateBillCommand;
use App\DTO\Orders\CreateBillDTO;
use App\Exceptions\ApiExceptions\Points\AvailabilityOfPointsChangedException;
use App\Managers\BillManager;
use App\Managers\DeliveryRangeManager;
use App\Models\Bill;
use App\Repositories\Interfaces\BillRepositoryInterface;
use Ecotone\Modelling\Attribute\CommandHandler;
use Ecotone\Modelling\CommandBus;

class CreateBillHandler
{
    public function __construct(
        protected CommandBus $commandBus,
        protected BillRepositoryInterface $billRepository,
    ) {
    }

    #[CommandHandler]
    public function createBill(CreateBillCommand $command): void
    {
        $billDto = $command->getBillDto();

        $this->setTotalPrice($billDto);
        $this->setDiscount($billDto);
        $this->addPackagePrice($billDto);
        $this->setServiceCharge($billDto);
        $this->setDeliveryCost($billDto);

        $createdBill = $this->billRepository->createBill($billDto->toArray());

        $this->spendUserPoints($createdBill);

        $billDto->setId($createdBill->id);
    }

    protected function spendUserPoints(Bill $bill): void
    {
        if ($bill->points > 0) {
            $result = $bill->spendPoints($bill->points);

            if ($result === false) {
                throw new AvailabilityOfPointsChangedException();
            }
        }
    }

    protected function setTotalPrice(CreateBillDTO $billData): void
    {
        $billData->setTotalPrice(BillManager::getTotalFoodPrice($billData));
    }

    protected function setDiscount(CreateBillDTO $billData): void
    {
        $billData->setDiscount(BillManager::getTotalDiscount($billData));
    }

    protected function setDeliveryCost(CreateBillDTO $billData): void
    {
        $billData->setDeliveryCost(DeliveryRangeManager::getDeliveryCost($billData));
    }

    protected function setServiceCharge(CreateBillDTO $billData): void
    {
        $billData->setServiceCharge(BillManager::getServiceCharge($billData));
    }

    protected function addPackagePrice(CreateBillDTO $billData): void
    {
        $billData->setTotalPrice($billData->getTotalPrice() + BillManager::getPackageCost($billData));
    }
}
