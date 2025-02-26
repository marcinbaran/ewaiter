<?php

namespace App\Http\Livewire\Marketplace;

use Livewire\Component;

class Checkout extends Component
{
    public $step = 1;
    public $isComplete = false;
    protected $listeners = ['nextStep', 'backStep'];

    public function handleNextStep()
    {
        if ($this->step === 1) {
            $this->emitTo('marketplace.checkout-address-form', 'addressValidationNextStep');
        } elseif ($this->step === 2) {
            $this->emitTo('marketplace.shipping-card', 'shippingValidationNextStep');
        } elseif ($this->step === 3) {
            $this->emitTo('marketplace.payment-card', 'paymentValidationNextStep');
        } elseif ($this->step === 4) {
            $this->emitTo('marketplace.summary-card', 'summaryValidation');
        }
    }

    public function backStep()
    {
        $this->step--;
    }

    public function nextStep()
    {
        $this->step++;
    }

    public function render()
    {
        return view('livewire.marketplace.checkout', [
            'step' => $this->step,
        ]);
    }


}
