<?php

namespace App\Http\Livewire\Marketplace;

use App\Services\Marketplace\OrdersService;
use Livewire\Component;

class AddSubButton extends Component
{
    public $quantity = 1;
    public $productId;
    public $isInCart = false;
    public bool $isBackground = true;

    protected $orderService;
    protected $miniCartCard;

    public function boot(OrdersService $ordersService, MiniCartCard $miniCartCard)
    {
        $this->orderService = $ordersService;
        $this->miniCartCard = $miniCartCard;
    }

    public function mount(OrdersService $ordersService, int $id)
    {
        $this->productId = $id;
    }

    public function increaseQuantity()
    {
        $this->quantity++;

        if ($this->isInCart) {
//            dd($this->orderService);
//            $this->orderService->updateCartItem($this->productId, $this->quantity);
            $this->emitTo('marketplace.mini-cart-card', 'updatedQuantity', $this->productId, $this->quantity);
        } else {
            $this->emit('updateQuantity', $this->productId, $this->quantity);
        }
    }

    public function decreaseQuantity()
    {
        if ($this->quantity > 0) {

            if ($this->isInCart) {
                $this->quantity--;
                $this->emitTo('marketplace.mini-cart-card', 'updatedQuantity', $this->productId, $this->quantity);
//                dump($this->productId);
            } elseif ($this->quantity > 1) {
                $this->quantity--;
                $this->emit('updateQuantity', $this->productId, $this->quantity);
            }
        }
    }

    private function updateQuantity()
    {
        $this->emitUp('updateQuantity', $this->productId, $this->quantity);
    }

    public function render()
    {
        return view('livewire.marketplace.add-sub-button');
    }
}
