<?php

namespace App\DTO\Orders;

use App\Enum\DeliveryMethod;
use App\Enum\PaymentType;
use App\Http\Requests\Api\PlaceOrderRequest;
use App\Http\Requests\Api\ValidateCartRequest;

final class ValidateBillDTO extends BillDTO
{
    public function __construct(
        protected readonly array $orders,
        protected readonly ?PaymentType $paymentType,
        protected readonly ?int $points,
        protected readonly ?float $pointsValue,
        protected readonly ?DeliveryMethod $deliveryMethod,
        protected readonly ?string $deliveryTime,
        protected readonly ?string $phone,
        protected readonly int $userId,
        protected readonly ?int $addressId = null,
        protected readonly ?string $comment = null,
        protected readonly ?string $tableNumber = null,
        protected readonly ?string $roomNumber = null,
        protected readonly ?bool $personalPickup = null,
        protected readonly ?float $distance = null,
    ) {
    }

    public static function createFromRequest(ValidateCartRequest $request): self
    {
        $deliveryMethod = $request->get(PlaceOrderRequest::DELIVERY_PARAM_KEY) ? DeliveryMethod::tryFrom($request->get(PlaceOrderRequest::DELIVERY_PARAM_KEY)['type']) : null;
        $pointsValue = self::hasUsePoints($request) ? self::getPointsValue($request->get(PlaceOrderRequest::USE_POINTS_PARAM_KEY)) : 0;

        return new self(
            orders:         $request->get(PlaceOrderRequest::ORDERS_PARAM_KEY),
            paymentType:    PaymentType::tryFrom($request->get(PlaceOrderRequest::PAYMENT_TYPE_PARAM_KEY)),
            points:         $request->get(PlaceOrderRequest::USE_POINTS_PARAM_KEY),
            pointsValue:    $pointsValue,
            deliveryMethod: $deliveryMethod,
            deliveryTime:   $request->get(PlaceOrderRequest::DELIVERY_TIME_PARAM_KEY),
            phone:          $request->get(PlaceOrderRequest::PHONE_PARAM_KEY),
            userId:         auth()->user()->id,
            comment:        $request->get(PlaceOrderRequest::COMMENT_PARAM_KEY),
            tableNumber:    $deliveryMethod === DeliveryMethod::TABLE_DELIVERY ? $request->get(PlaceOrderRequest::DELIVERY_PARAM_KEY)['value'] : null,
            roomNumber:     $deliveryMethod === DeliveryMethod::ROOM_DELIVERY ? $request->get(PlaceOrderRequest::DELIVERY_PARAM_KEY)['value'] : null,
            personalPickup: $deliveryMethod === DeliveryMethod::PERSONAL_PICKUP,
            distance:       self::getDistance($request, $deliveryMethod),
        );
    }

    public static function hasUsePoints(ValidateCartRequest $request): bool
    {
        return $request->get(PlaceOrderRequest::USE_POINTS_PARAM_KEY) !== null && $request->get(PlaceOrderRequest::USE_POINTS_PARAM_KEY) > 0;
    }
}
