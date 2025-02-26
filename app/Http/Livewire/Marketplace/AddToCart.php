<?php

namespace App\Http\Livewire\Marketplace;

use App\Services\Marketplace\OrdersService;
use Livewire\Component;

class AddToCart extends Component
{
    private $orderService;
    private $miniCartCard;

    public $variantCode;
    public $quantity = 1;
    public $productId;

    protected $listeners = ['updateQuantity'];

    public function boot(OrdersService $orderService, MiniCartCard $miniCartCard)
    {
        $this->orderService = $orderService;
        $this->miniCartCard = $miniCartCard;
    }

    public function mount($variantCode, $id)
    {
        $this->variantCode = $variantCode;
        $this->productId = $id;
    }

    public function addToCart()
    {
//        dd($this->variantCode, $this->quantity);
//        if ($this->miniCart->miniCart){}
//        $this->miniCartCard->getMiniCart('add', $this->variantCode, $this->quantity);
//        dump($this->miniCartCard->miniCart);
//        $this->orderService->addToCart($this->variantCode, $this->quantity);
        $this->emitTo('marketplace.mini-cart-card', 'productAdded', $this->variantCode, $this->quantity);
        $this->emitTo('marketplace.mini-cart-card', 'updatedSummary');
    }

    public function updateQuantity($id, $quantity)
    {
        if ($this->productId === $id) {
            $this->quantity = $quantity;
        }
    }

    public function render()
    {
        return view('livewire.marketplace.add-to-cart');
    }

    public function hydrate()
    {
        $this->miniCartCard = app()->make(MiniCartCard::class);
        $this->orderService = app()->make(OrdersService::class);
    }
}
