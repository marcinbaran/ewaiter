<?php

namespace App\Http\Livewire\Marketplace;

use App\Services\Marketplace\CheckoutService;
use Illuminate\Support\Facades\Session;
use Livewire\Component;

class PaymentCard extends Component
{
    public $step;
    public $data;
    public $selectedMethod = [
        'code' => '',
        'id' => '',
    ];
    public $availablePaymentMethods;
    protected $rules = [
        'selectedMethod.code' => 'required',
    ];
    protected $listeners = ['paymentValidationNextStep' => 'nextStep'];
    private $checkoutService;

    public function boot(CheckoutService $checkoutService)
    {
        $this->checkoutService = $checkoutService;
    }

    public function mount($step)
    {
        $this->data = Session::get('cart-data');
        $this->getPaymentMethods();
        $this->step = $step;
    }

    public function getPaymentMethods()
    {
        $this->availablePaymentMethods = $this->checkoutService->responseToArray(
            $this->checkoutService->getPaymentMethods($this->data['payments'][0]['id'])
//            $this->checkoutService->getPaymentMethods()
        );
    }

    public function changeMethod($methodCode)
    {
//        dd($this->selectedMethod);
        $this->selectedMethod['code'] = $methodCode;
    }

    public function backStep()
    {
        $this->emitUp('backStep');
    }

    public function nextStep()
    {
        $this->validate();
        $this->confirmPayment();
        $this->emitTo('marketplace.checkout', 'nextStep');
    }

    public function confirmPayment()
    {
        $response = $this->checkoutService->responseToArray($this->checkoutService->setPaymentsMethods($this->data['payments'][0]['id'], $this->selectedMethod['code']));
        Session::put('payment-data', $response);
    }

    public function render()
    {
        return view('livewire.marketplace.payment-card');
    }

}
