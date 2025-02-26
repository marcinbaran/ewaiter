<?php

namespace App\Http\Livewire\Marketplace;

use Livewire\Component;

class MiniCartItem extends Component
{
    public $productId;
    public $name;
    public $quantity;
    public $totalPrice;
    public $size;
    public $image;
    protected $miniCartCard;
    protected $listeners = ['updatedPrice' => 'updatePrice'];

    public function mount(MiniCartCard $miniCartCard ,$id, $name, $quantity, $totalPrice, $size, $image)
    {
        $this->productId = $id;
        $this->name = $name;
        $this->quantity = $quantity;
        $this->totalPrice = $totalPrice;
        $this->size = $size;
        $this->image = $image;
        $this->miniCartCard = $miniCartCard;
    }

    public function updatePrice($price)
    {
//        foreach ( as $cartItem) {}
        $this->totalPrice = $price;
    }

    public function updateQuantity($quantity)
    {
        $this->quantity = $quantity;
    }

    public function render(){
       return view('livewire.marketplace.mini-cart-item');
    }
}
