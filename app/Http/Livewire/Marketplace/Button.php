<?php

namespace App\Http\Livewire\Marketplace;

use Livewire\Component;

class Button extends Component
{
    public $icon;

    protected $miniCartCard;

    public function mount(MiniCartCard $miniCartCard, $icon = '')
    {
        $this->miniCartCard = $miniCartCard;
        $this->icon = $icon;
    }

    public function removeCart()
    {
        $this->miniCartCard->removeAllItemsFromCart();
    }

    public function render()
    {
        return view('livewire.marketplace.button');
    }
}
