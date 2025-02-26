<?php

namespace App\Http\Livewire\Marketplace;

use App\Services\Marketplace\OrdersService;
use Livewire\Component;

class ProductContent extends Component
{
    public $variants;
    public $name;
    public $productId;
    public $quantityInput = '';
    public $currentVariantCode;
    public $currentVariantName;
    public $currentVariantPrice;

    protected $orderService;

    public function mount(OrdersService $orderService, $name, $id, $variants)
    {
        $this->orderService = $orderService;
        $this->variants = $variants;
        $this->name = $name;
        $this->productId = $id;
        $this->currentVariantCode = $this->findFirstAvailable()->code;
        $this->currentVariantPrice = $this->findFirstAvailable()->price;
        $this->currentVariantName = $this->findFirstAvailable()->name;
    }

    private function findFirstAvailable()
    {
        foreach ($this->variants as $variant) {
            if ($variant->inStock) {
                return $variant;
            }
        }
    }

    public function addToCart()
    {
        $quantity = (int)$this->quantityInput;
        $this->orderService->addToCart($this->currentVariantCode, $quantity);
    }

    public function changeVariant($variantCode)
    {
        $this->currentVariantCode = $variantCode;
        $this->currentVariantPrice = $this->getCurrentVariant($variantCode)->price;
        $this->currentVariantName = $this->getCurrentVariant($variantCode)->name;
    }

    private function getCurrentVariant($variantCode)
    {
        foreach ($this->variants as $variant) {
            if ($variant->code == $variantCode) {
                return $variant;
            }
        }
    }

    public function clearInput()
    {
        $this->quantityInput = '';
    }

    public function hydrate()
    {
        $this->variants = array_map(function ($variant) {
            return (object)$variant;
        }, $this->variants);
        $this->orderService = app()->make(OrdersService::class);
    }

    public function render()
    {
        return view('livewire.marketplace.product-content');
    }
}
