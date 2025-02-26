<?php

namespace App\DTO\Orders;

use App\Enum\DeliveryMethod;
use App\Enum\OrderStatus;
use App\Http\Requests\Api\PlaceOrderRequest;
use App\Models\Addition;
use App\Models\Settings;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

final class CreateOrderDTO
{
    public function __construct(
        private readonly int    $billId,
        private readonly int    $quantity,
        private readonly float  $price,
        private readonly float  $discount,
        private readonly int    $itemId,
        private readonly string $itemName,
        private readonly int    $userId,
        private readonly int    $status,
        private readonly float  $additionsPrice,
        private readonly float  $packagePrice,
        private readonly array  $additionalInfo,
        private readonly string $type,
        private readonly array  $productsInOrder,
    )
    {
    }

    public static function createFromArray(array $order, int $billId, float $packagePrice): self
    {
        return new self(
            billId: $billId,
            quantity: $order[PlaceOrderRequest::QUANTITY_PARAM_KEY],
            price: (isset($order[PlaceOrderRequest::DISH_PARAM_KEY][PlaceOrderRequest::DISH_PRICE_PARAM_KEY])) ? $order[PlaceOrderRequest::DISH_PARAM_KEY][PlaceOrderRequest::DISH_PRICE_PARAM_KEY] : $order[PlaceOrderRequest::BUNDLE_PARAM_KEY][PlaceOrderRequest::DISH_PRICE_PARAM_KEY],
            discount: (isset($order[PlaceOrderRequest::PROMOTION_PARAM_KEY])) ? $order[PlaceOrderRequest::PROMOTION_PARAM_KEY][PlaceOrderRequest::PROMOTION_DISCOUNT_PARAM_KEY][PlaceOrderRequest::PROMOTION_DISCOUNT_AMOUNT_PARAM_KEY] : 0,
            itemId: (isset($order[PlaceOrderRequest::DISH_PARAM_KEY][PlaceOrderRequest::DISH_ID_PARAM_KEY])) ? $order[PlaceOrderRequest::DISH_PARAM_KEY][PlaceOrderRequest::DISH_ID_PARAM_KEY] : $order[PlaceOrderRequest::BUNDLE_PARAM_KEY][PlaceOrderRequest::DISH_ID_PARAM_KEY],
            itemName: (isset($order[PlaceOrderRequest::DISH_PARAM_KEY][PlaceOrderRequest::DISH_NAME_PARAM_KEY])) ? $order[PlaceOrderRequest::DISH_PARAM_KEY][PlaceOrderRequest::DISH_NAME_PARAM_KEY] : $order[PlaceOrderRequest::BUNDLE_PARAM_KEY][PlaceOrderRequest::DISH_NAME_PARAM_KEY],
            userId: Auth::user()->id,
            status: OrderStatus::NEW->value,
            additionsPrice: self::getAdditionsPrice($order),
            packagePrice: $packagePrice,
            additionalInfo: self::getAdditionalInfo($order),
            type: (isset($order[PlaceOrderRequest::DISH_PARAM_KEY])) ? PlaceOrderRequest::DISH_PARAM_KEY : PlaceOrderRequest::BUNDLE_PARAM_KEY,
            productsInOrder: self::getProductsInOrder((isset($order[PlaceOrderRequest::DISH_PARAM_KEY])) ? PlaceOrderRequest::DISH_PARAM_KEY : PlaceOrderRequest::BUNDLE_PARAM_KEY,$order),
        );
    }

    public static function createCollectionFromRequest(PlaceOrderRequest $request, int $billId): Collection
    {
        $ordersCollection = new Collection();
        foreach ($request->orders as $order) {
            $ordersCollection->add(self::createFromArray($order, $billId, self::getPackagePrice(DeliveryMethod::from($request->get(PlaceOrderRequest::DELIVERY_PARAM_KEY)['type']))));
        }

        return $ordersCollection;
    }

    public function toArray(): array
    {
        return [
            'bill_id' => $this->billId,
            'quantity' => $this->quantity,
            'price' => $this->price,
            'discount' => $this->discount,
            'item_id' => $this->itemId,
            'item_name' => $this->itemName,
            'user_id' => $this->userId,
            'status' => $this->status,
            'additions_price' => $this->additionsPrice,
            'package_price' => $this->packagePrice,
            'customize' => $this->additionalInfo,
            'type' => $this->type,
            'products_in_order' => $this->productsInOrder,
        ];
    }

    private static function getAdditionalInfo(array $order): array
    {
        $result['additions'] = [];

        foreach ($order['additions'] as $requestAddition) {
            $dbAddition = Addition::findOrFail($requestAddition['id']);

            $result['additions'][] = [
                'id' => $dbAddition->id,
                'name' => $dbAddition->name,
                'price' => $dbAddition->price,
                'type' => $dbAddition->type,
                'quantity' => $requestAddition['quantity'],
                'dish_id' => $requestAddition['dish_id']??null,
            ];
        }
        return $result;
    }

    private static function getAdditionsPrice(array $order): float
    {
        $totalAdditionsPrice = 0;

        foreach ($order['additions'] as $requestAddition) {
            $totalAdditionsPrice += Addition::findOrFail($requestAddition['id'])->price * $order['quantity'];
        }

        return $totalAdditionsPrice;
    }

    private static function getPackagePrice(DeliveryMethod $deliveryMethod): float
    {
        if (in_array($deliveryMethod, [DeliveryMethod::PERSONAL_PICKUP, DeliveryMethod::DELIVERY_TO_ADDRESS])) {
            $packagePrice = Settings::getSetting('koszt_opakowania', 'koszt_opakowania', true);

            if ($packagePrice === null) {
                $packagePrice = 0;
            }

            return $packagePrice;
        }

        return 0;
    }
    // COMENTED FRAGMENT ADD ADDITIONS IN TO SPECIFIC DISH
    private static function getProductsInOrder(string $type,array $order): array
    {
        $productsInOrder = collect();
//        $additions = self::getAdditionalInfo($order);
//        $groupedAdditions = [];
//        foreach ($additions['additions'] as $addition) {
//            $groupedAdditions[$addition['dish_id']][] = $addition;
//        }
        if ($type == PlaceOrderRequest::DISH_PARAM_KEY) {
            $dish = isset($order[PlaceOrderRequest::DISH_PARAM_KEY]) ? $order[PlaceOrderRequest::DISH_PARAM_KEY] : null;
            if ($dish) {
                $productsInOrder[] = [
                    'id' => $dish['id'],
                    'name' => $dish['name'],
                    'description' => $dish['description'],
                    'price' => $dish['price'],
                    'promotion' => $dish['promotion'],
                    'food_category_id' => $dish['food_category_id'],
//                    'additions' => isset($groupedAdditions[$dish['id']]) ? $groupedAdditions[$dish['id']] : [],
                ];
            }
        } else if ($type == PlaceOrderRequest::BUNDLE_PARAM_KEY) {
            $bundle = isset($order[PlaceOrderRequest::BUNDLE_PARAM_KEY]) ? $order[PlaceOrderRequest::BUNDLE_PARAM_KEY] : null;
            if ($bundle && isset($bundle['dishes'])) {
                foreach ($bundle['dishes'] as $dish) {
                    $productsInOrder[] = [
                        'id' => $dish['id'],
                        'name' => $dish['name'],
                        'description' => $dish['description'],
                        'price' => $dish['price'],
                        'promotion' => $dish['promotion'],
                        'food_category_id' => $dish['food_category_id'],
//                        'additions' => isset($groupedAdditions[$dish['id']]) ? $groupedAdditions[$dish['id']] : [],
                    ];
                }
            }
        }
        return json_decode($productsInOrder);
    }
}
