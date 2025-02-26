<?php

namespace App\Services\Marketplace;


use GuzzleHttp\Exception\ClientException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class CheckoutService extends MarketplaceService
{


    public function setAddressforCheckout(array $address)
    {
        try {
            $cartCode = Cache::get('cart_token_value_' . auth()->user()->id);
            $options = $this->getHeaders();
            $options['json'] = [
                "email" => Auth::user()->email,
                "billingAddress" => $address,
                "shippingAddress" => $address,
                "couponCode" => null,
            ];
            return $this->put('/api/v2/shop/orders/' . $cartCode, 200, $options);
        } catch (ClientException $e) {
            dd($e->getResponse());
        }

    }

    public function getShippmentMethods(string $shipmentId)
    {
        $cartCode = Cache::get('cart_token_value_' . auth()->user()->id);
        $options = $this->getHeaders();

//        return $this->get('/api/v2/shop/shipping-methods?shipmentId=' . $shipmentId . "&tokenValue=" . $cartCode, 200, $options);
        return $this->get('/api/v2/shop/orders/'.$cartCode.'/shipments/'.$shipmentId.'/methods?page=1&itemsPerPage=30', 200, $options);
//        return $this->get('/api/v2/shop/shipping-methods/', 200, $options);
    }

    public function setShippmentsMethods(string $shipmentId, string $shippingMethod)
    {

        $cartCode = Cache::get('cart_token_value_' . auth()->user()->id);
//        $options = $this->getHeaders();
//        $options['Accept'] = 'application/ld+json';
//        $options['Content-Type'] = 'application/merge-patch+json';
        $options['json'] = [
            "shippingMethod" => $shippingMethod
        ];
//            dd($options,$cartCode,$shipmentId,$shippingMethod);
        return $this->patch('/api/v2/shop/orders/' . $cartCode . "/shipments/" . $shipmentId, 200, $options);
    }


    public function getPaymentMethods(string $paymentId)
    {
        $cartCode = Cache::get('cart_token_value_' . auth()->user()->id);
        $options = $this->getHeaders();
//        return $this->get('/api/v2/shop/payment-methods?paymentId=' . $paymentId . '&tokenValue=' . $cartCode, 200, $options);
        return $this->get('/api/v2/shop/orders/'.$cartCode.'/payments/'.$paymentId.'/methods?page=1&itemsPerPage=30', 200, $options);
//        return $this->get('/api/v2/shop/payment-methods/', 200, $options);
    }

    public function setPaymentsMethods(string $paymentId, string $paymentMethod)
    {
        $cartCode = Cache::get('cart_token_value_' . auth()->user()->id);
//
//        $options = $this->getHeaders();
        $options['json'] = [
            "paymentMethod" => $paymentMethod
        ];
        $res =$this->patch('/api/v2/shop/orders/' . $cartCode . "/payments/" . $paymentId, 200, $options);
        return $res;
    }


    public function completeOrder(string $notes)
    {
        $cartCode = Cache::get('cart_token_value_' . auth()->user()->id);
        $options = $this->getHeaders();
        $options['headers']['accept'] = 'application/ld+json';
        $options['headers']['Content-Type'] = 'application/merge-patch+json';
        $options['json'] = [
            "notes" => $notes
        ];
//        dd($options);
        Cache::forget('cart_token_value_' . auth()->user()->id);


        $this->patch('/api/v2/shop/orders/' . $cartCode . '/complete', 200, $options);

        //TODO:  PRZEKIEROWANIE DO ZAMÓWIENIA W HISTORI ZAMÓWIEŃ
    }


    public function getMethodCode($method)
    {
        $methodName = strtolower(basename($method));
        $methodName = str_replace(' ', '_', $methodName);

        return $methodName;
    }

    public function getTransactionData(string $method, array $transaction)
    {
        $transactionMethods = [];
        if ($method === 'payment') {
            $transactionMethods = $this->responseToArray($this->getPaymentMethods($transaction['id']));
        } elseif ($method === 'shipment') {
            $transactionMethods = $this->responseToArray($this->getShippmentMethods($transaction['id']));
//            dd($transactionMethods);
        }

        $methodCode = '';
        if (is_string($transaction['method'])) {
            $methodCode = $this->getMethodCode($transaction['method']);
        } elseif (is_array($transaction['method'])) {
            $methodCode = $this->getMethodCode($transaction['method']['name']);
        }

        foreach ($transactionMethods as $transactionMethod) {
            if ($transactionMethod['code'] === $methodCode) {
                return $transactionMethod;
            }
        }
    }

    public function getShippingData(array $shipments)
    {
        $shippingMethods = [];
        $shippingMethods = $this->responseToArray($this->getShippmentMethods($shipments['id']));


    }


//{
//  "email": "string",
//  "billingAddress": {
//    "id": 71,
//    "firstName": "Rafał",
//    "lastName": "Wątroba",
//    "phoneNumber": "123123123",
//    "company": "Rafałowa",
//    "countryCode": "PL",
//    "provinceCode": "PL-PK",
//    "provinceName": "podkarpackie",
//    "street": "Dotnetowa 420",
//    "city": "Rzeszów",
//    "postcode": "69-420"
//},
//  "shippingAddress": {
//    "id": 71,
//    "firstName": "Rafał",
//    "lastName": "Wątroba",
//    "phoneNumber": "123123123",
//    "company": "Rafałowa",
//    "countryCode": "PL",
//    "provinceCode": "PL-PK",
//    "provinceName": "podkarpackie",
//    "street": "Dotnetowa 420",
//    "city": "Rzeszów",
//    "postcode": "69-420"
//},
//  "couponCode": null
//}


}

