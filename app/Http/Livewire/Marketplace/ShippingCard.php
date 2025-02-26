<?php

namespace App\Http\Livewire\Marketplace;

use App\Services\Marketplace\CheckoutService;
use Illuminate\Support\Facades\Session;
use Livewire\Component;

class ShippingCard extends Component
{
    public $step;
    public $data;
    public $code;
    public $selectedMethod = [
        'code' => '',
        'id' => ''
    ];
    public $availableShippingMethods;

    protected $rules = [
        'selectedMethod.code' => 'required',
    ];
    protected $listeners = ['shippingValidationNextStep' => 'nextStep'];

    private $checkoutService;

    public function boot(CheckoutService $checkoutService)
    {
        $this->checkoutService = $checkoutService;
    }

    public function mount($step)
    {
        $this->data = Session::get('cart-data');
//        dd($this->data['shipments']);
        $this->getShippmentMethods();
        $this->step = $step;
    }

    public function getShippmentMethods()
    {

        //TODO: IF NO SHIPPMENTS NOT AVAILABLE ORDER
//        dd($this->data);
        $this->availableShippingMethods = $this->checkoutService->responseToArray(
            $this->checkoutService->getShippmentMethods($this->data['shipments'][0]['id'])
        //            $this->checkoutService->getShippmentMethods()
        );

//        dd($this->availableShippingMethods);
    }

    public function render()
    {
        return view('livewire.marketplace.shipping-card');
    }

    public function changeMethod($methodCode)
    {
        $this->selectedMethod['code'] = $methodCode;
    }

    public function backStep()
    {
        $this->emitUp('backStep');
    }

    public function nextStep()
    {
        $this->validate();
        $this->confirmShipping();
        $this->emitTo('marketplace.checkout', 'nextStep');
    }

    public function confirmShipping()
    {
        $response = $this->checkoutService->responseToArray($this->checkoutService->setShippmentsMethods($this->data['shipments'][0]['id'], $this->selectedMethod['code']));
//        dd($response);
        Session::put('shipping-data', $response);
    }
}
