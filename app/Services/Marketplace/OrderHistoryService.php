<?php

namespace App\Services\Marketplace;

use Illuminate\Support\Facades\Cache;
use function Clue\StreamFilter\append;
use App\Services\Marketplace\ProductsService;

class OrderHistoryService extends MarketplaceService
{

    public function getOrdersHistory(int $page = 1, int $itemsPerPage = 30)
    {
        $response = $this->responseToarray(array_reverse($this->get('/api/v2/shop/orders?page='.$page.'&itemsPerPage='.$itemsPerPage, 200)));
        return $response;
    }

    private function OrderHistoryAddAdditionalInformations(array $order)
    {
        $receivedOrder = $this->responseToArray($this->get('/api/v2/shop/orders/' . $order['tokenValue'], 200));
       return $this->getOrderHistoryDetails($receivedOrder['tokenValue']);
    }

    public function getOrderHistoryDetails(string $orderTokenValue)
    {
        $receivedOrder = $this->responseToArray($this->get('/api/v2/shop/orders/' . $orderTokenValue, 200));

        $receivedPayments = $this->responseToArray($this->get('/api/v2/shop/payments/'.$receivedOrder['payments'][0]['id'], 200));
        $receivedShipments = $this->responseToArray($this->get('/api/v2/shop/shipments/'.$receivedOrder['shipments'][0]['id'], 200));
        $parsedItems = $this->parseItems($receivedOrder['items']);
        $receivedOrder['payments'] = $receivedPayments;
        $receivedOrder['shipments'] = $receivedShipments;
        $receivedOrder['items'] = $parsedItems;
        return $receivedOrder;
    }

    private function parseItems(array $items ): array
    {
        $parsedItems=[];
        foreach ($items as $item) {

                $variant=$this->responseToArray($this->get($item['variant'], 200));

            $item['variant']=$variant;
            $item['productImageUrl']=$this->GetItemImageUrl($item);
//            dump($item['productImageUrl']);
            array_push($parsedItems, $item);
        }
        return $parsedItems;
    }



    private function GetItemImageUrl(array $items)
    {
        $imageSlug=$this->toSlug($items['productName']);
        $response=$this->get('/api/v2/shop/products-by-slug/'.$imageSlug,200);
        $productData=$this->responseToArray($response);
//        return $this->EncodeToBase64($productData['images'][0]['path']);
        return $productData['images'][0]['path'];
    }

}

