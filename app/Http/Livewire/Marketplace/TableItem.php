<?php

namespace App\Http\Livewire\Marketplace;

use AllowDynamicProperties;
use App\Services\Marketplace\OrderHistoryService;
use Livewire\Component;

class TableItem extends Component
{
    public $order;
    public bool $loadData = false;
    public $orderDetails = [];
    private $orderHistoryService;

    public function mount(array $order, OrderHistoryService $orderHistoryService)
    {
        $this->orderHistoryService = $orderHistoryService;
        $this->order = $order;

    }
    public function hydrate()
    {
        $this->orderHistoryService = app()->make(OrderHistoryService::class);
    }
    public function loadOrderDetails(string $orderTokenValue)
    {
        $this->orderDetails = $this->orderHistoryService->getOrderHistoryDetails($orderTokenValue);
        $this->loadData = true;
    }

    public function render()
    {
        return view('livewire.marketplace.table-item')->with('orderDetails', $this->orderDetails);
    }
}
