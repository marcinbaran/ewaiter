<?php

namespace App\Http\Livewire\Marketplace;

use Livewire\Component;

class MiniCartSummary extends Component
{
    public $totalPrice;
    protected $listeners = ['updatedTotalPrice' => 'updateTotalPrice'];

    public function updateTotalPrice($totalPrice)
    {
        $this->totalPrice = $totalPrice;
    }


    public function render()
    {
        return view('livewire.marketplace.mini-cart-summary');
    }
}
