<?php

namespace App\Http\Livewire\Marketplace;

use App\Services\Marketplace\OrdersService;
use App\Services\Marketplace\ProductsService;
use Illuminate\Routing\Route;
use Livewire\Component;

class MiniCartCard extends Component
{
    public $miniCart;
    public $isCheckout = false;
    public $miniCartItem = [
        'id' => 0,
        'productName' => '',
        'variant' => '',
        'quantity' => 1,
        'unitPrice' => 0,
        'totalPrice' => 0,
        'size' => '',
        'image' => ''
    ];
    public $miniCartItems = [];
    public $miniCartItemsWithImg;
    protected $orderService;
    protected $productService;
    protected $listeners = ['productAdded' => 'addProduct', 'updatedQuantity' => 'updateQuantity'];

    public function boot(OrdersService $orderService, ProductsService $productService)
    {
        $this->initService($orderService, $productService);
    }

    public function mount($isCheckout = false)
    {
        $this->isCheckout = $isCheckout;
    }

    public function initService(OrdersService $orderService, ProductsService $productService)
    {
        $this->orderService = $orderService;
        $this->productService = $productService;
        $this->loadMiniCart();
    }

    public function addProduct($variantCode, $quantity)
    {
        foreach ($this->miniCartItems as $cartItem) {
            if ($cartItem->variant === $variantCode) {
                $cartItem->quantity += $quantity;
                $this->loadMiniCart();
                return;
            }
        }

        $this->orderService->addToCart($variantCode, $quantity);
        $this->loadMiniCart();
//        dd($this->miniCartItems);
    }

    public function updateQuantity($id, $quantity)
    {
        if ($quantity > 1) {
            $this->orderService->updateCartItem($id, $quantity);
        } elseif ($quantity === 0) {
            $this->orderService->removeFromCart($id);
        }
        $this->loadMiniCart();
    }

    public function loadMiniCart()
    {
        $this->miniCart = $this->orderService->responseToArray($this->orderService->showCart());
//        $this->miniCart = $this->orderService->showCart();
        $this->miniCartItemsWithImg = $this->getImagesForMiniCartItems($this->miniCart);
        $this->miniCartItems = $this->duplicateMiniCart($this->miniCart, $this->miniCartItemsWithImg);
//        $this->emitTo('marketplace.mini-cart-summary', 'updatedTotalPrice', $this->getSummary());
        $this->getSummary();
    }

    private function getSummary()
    {
        $totalPrice = 0;

        foreach ($this->miniCart as $item) {
            $totalPrice += $item['subtotal'];
        }
        return $totalPrice;
    }

    private function getImagesForMiniCartItems($miniCart)
    {
        $miniCartItems = [];

        foreach ($miniCart as $cartItem) {
            $cartItem = (object)$cartItem;
            $product = $this->productService->getProductBySlug($cartItem->productName);

            $miniCartItems[] = $product;
        }
//        dd($miniCartItems);
        return $this->productService->getImagesForLatestProducts($miniCartItems);
    }

//    creates new array of objects duplicating $miniCart item but with added property image
    private function duplicateMiniCart($miniCart, $miniCartWithImg)
    {
        $newMiniCart = [];
        foreach ($miniCart as $key => $cartItem1) {
            $cartItem2 = $miniCartWithImg[$key];
            $product = (object)$cartItem1;
            $product->subtotal = number_format($cartItem1['subtotal'] / 100, 2, ',');
            $product->size = $this->productService->getVariant($product->variant)->name;
            $product->image = $cartItem2->images[0];
            $newMiniCart[] = $product;
        }

        return $newMiniCart;
    }

    public function increaseQuantity($id)
    {
        foreach ($this->miniCartItems as $cartItem) {
            if ($cartItem->id === $id) {
                $cartItem->quantity++;
                $this->updateQuantity($cartItem->id, $cartItem->quantity);
            }
        }
    }

    public function decreaseQuantity($id)
    {
        foreach ($this->miniCartItems as $cartItem) {
            if ($cartItem->id === $id) {
                $cartItem->quantity--;
                $this->updateQuantity($cartItem->id, $cartItem->quantity);

            }
        }
    }

    public function removeAllItemsFromCart()
    {
//        dd($this->miniCart, $this->miniCartItems);
        $this->orderService->removeCart();
        $this->loadMiniCart();
        if ($this->isCheckout) {
            redirect(route('admin.marketplace.index'));
        }
    }

    public function hydrate()
    {
        $orderService = app()->make(OrdersService::class);
        $productService = app()->make(ProductsService::class);
        $this->initService($orderService, $productService);
        $this->loadMiniCart();
    }

    public function render()
    {
        return view('livewire.marketplace.mini-cart-card');
    }
}
