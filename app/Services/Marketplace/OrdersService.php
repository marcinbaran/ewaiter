<?php

namespace App\Services\Marketplace;

use Illuminate\Support\Facades\Cache;

class OrdersService extends MarketplaceService
{


    public function addToCart(string $productCode, int $quantity)
    {
        $cartTokenValue = $this->getCartToken();
        $options = [
            'headers' => [
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . $this->token()
            ],
            'json' => [
                'productVariant' => '/api/v2/shop/product-variants/' . $productCode,
                'quantity' => $quantity
            ]
        ];
        return $this->post('/api/v2/shop/orders/' . $cartTokenValue . '/items', 201, $options);
    }

    public function getCartToken()
    {
        if (Cache::get('cart_token_value_' . auth()->user()->id) === null) {
            Cache::put('cart_token_value_' . auth()->user()->id, $this->createCart(), 86400);
        }
        $cartToken = Cache::get('cart_token_value_' . auth()->user()->id);
//        dump($cartToken);
//        dump($cartToken);


        return Cache::remember('cart_token_value_' . $cartToken, 86400, function () use ($cartToken) {
            return $cartToken;
        });
//        dd("getCartToken", $this->createCart());
        // dd("getCartToken " . auth()->user()->id);
//        return Cache::remember('cart_token_value_' . $this->createCart(), 86400, function () {
//            return $this->createCart();
//        });
    }

    private function createCart()
    {
        $token = $this->token();
        $options = [
            'headers' => [
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . $token
            ],
            'json' => [
                // 'channel' => '/api/v2/shop/channels/WEB',
                'localeCode' => __(config('services.marketplace.locale')),
            ]
        ];
        $res = $this->post('/api/v2/shop/orders', 201, $options);;
        if ($res) {

            $params = [
                'headers' => [
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json',
                    'Authorization' => 'Bearer ' . $this->token()
                ],
                'json' => [
                    'token' => $res->tokenValue,
                ]
            ];
            $cartChange= $this->post("/order-mode",204,$params);
            if ($cartChange) {
                return $res->tokenValue;
            }
        }
    }


    public function updateCartItem(int $orderItemId, int $quantity)
    {
        $cartTokenValue = $this->getCartToken();


        $options = [
            'json' => [
                'quantity' => $quantity
            ]
        ];
        return $this->patch('/api/v2/shop/orders/' . $cartTokenValue . '/items/' . $orderItemId, 200, $options);
    }

    public function showCart()
    {
        $cartTokenValue = $this->getCartToken();

        return $this->get('/api/v2/shop/orders/' . $cartTokenValue . '/items?page=1&itemsPerPage=30', 200);
    }

    public function removeFromCart(int $orderItemId)
    {
        $cartTokenValue = $this->getCartToken();

        return $this->delete('/api/v2/shop/orders/' . $cartTokenValue . '/items/' . $orderItemId, 204);
    }

    public function removeCart()
    {
        $cartTokenValue = $this->getCartToken();
        Cache::forget('cart_token_value_' . auth()->user()->id);
        $this->delete('/api/v2/shop/orders/' . $cartTokenValue, 204);
    }


    public function completeOrder()
    {
        $cartTokenValue = $this->getCartToken();

        return $this->patch('/api/v2/shop/orders/' . $cartTokenValue . '/complete', 200);
    }


}

