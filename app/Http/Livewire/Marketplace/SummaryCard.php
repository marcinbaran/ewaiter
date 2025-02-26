<?php

namespace App\Http\Livewire\Marketplace;

use App\Services\Marketplace\AddressService;
use App\Services\Marketplace\CheckoutService;
use App\Services\Marketplace\ProductsService;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use Livewire\Component;

class SummaryCard extends Component
{
    public $step;
    public $data;
    public $countryName;
    public $shipMethod;
    public $payMethod;
    public $notes;
    public $products;
    public $totalPrice;

    private $checkoutService;
    private $addressService;
    private $productService;
    protected $listeners = ['summaryValidation' => 'confirmCheckout'];

    public function boot(CheckoutService $checkoutService, AddressService $addressService, ProductsService $productsService)
    {
        $this->checkoutService = $checkoutService;
        $this->addressService = $addressService;
        $this->productService = $productsService;
    }

    public function mount($step)
    {
        $this->data = Session::get('payment-data');
        $this->countryName = $this->addressService->getCountryName($this->data['shippingAddress']['countryCode']);
        $this->payMethod = $this->checkoutService->getTransactionData('payment',$this->data['payments'][0]);
        $this->shipMethod = $this->checkoutService->getTransactionData('shipment',$this->data['shipments'][0]);
        $this->products = $this->productService->getProductsImageForCartSummary($this->data['items']);
//        $this->totalPrice = $this->checkoutService->getTotalPrice($this->data['items']);
//        dd($this->data);
        $this->notes = "";
        $this->step = $step;
    }

    public function render()
    {
        return view('livewire.marketplace.summary-card');
    }

    public function backStep()
    {
        $this->emitUp('backStep');
    }

    public function confirmCheckout()
    {
        $response = $this->checkoutService->responseToArray($this->checkoutService->completeOrder($this->notes));
        return Redirect::route('admin.marketplace.order_history_order_details', [
            'orderId' => $this->data['id'],
            'order' => $this->data['tokenValue']
        ]);
    }
}
