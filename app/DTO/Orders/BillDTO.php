<?php

namespace App\DTO\Orders;

use App\Enum\DeliveryMethod;
use App\Models\Restaurant;
use App\Services\GeoServices\GeoService;
use App\ValueObjects\Address;

abstract class BillDTO
{
    final protected const string POINTS_RATIO_CONFIG_PATH = 'admanager.ratio';

    protected ?int    $id = null;

    protected ?float  $totalPrice = null;

    protected float   $discount = 0;

    protected float   $deliveryCost = 0;

    protected float   $serviceCharge = 0;

    public function toArray()
    {
        return [
            'paid_type'         => $this->paymentType->value,
            'price'             => $this->totalPrice,
            'discount'          => $this->discount,
            'points'            => $this->points,
            'points_value'      => $this->pointsValue,
            'delivery_type'     => $this->deliveryMethod->value,
            'delivery_time'     => $this->deliveryTime,
            'delivery_cost'     => $this->deliveryCost,
            'phone'             => $this->phone,
            'user_id'           => $this->userId,
            'address_id'        => $this->addressId,
            'comment'           => $this->comment,
            'table_number'      => $this->tableNumber,
            'room_delivery'     => $this->roomNumber,
            'personal_pickup'   => $this->personalPickup,
            'service_charge'    => $this->serviceCharge,
        ];
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getTotalPrice(): float
    {
        return $this->totalPrice;
    }

    public function getDiscount(): float
    {
        return $this->discount;
    }

    public function getDeliveryMethod(): ?DeliveryMethod
    {
        return $this->deliveryMethod;
    }

    public function getOrders(): array
    {
        return $this->orders;
    }

    public function getClientDistance(bool $ceil = false): ?float
    {
        if ($this->distance === null) {
            return null;
        }

        if ($ceil) {
            return ceil($this->distance);
        }

        return $this->distance;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function setTotalPrice(float $totalPrice): void
    {
        $this->totalPrice = $totalPrice;
    }

    public function setDiscount(float $discount): void
    {
        $this->discount = $discount;
    }

    public function setServiceCharge(float $serviceCharge): void
    {
        $this->serviceCharge = $serviceCharge;
    }

    public function setDeliveryCost(float $deliveryCost): void
    {
        $this->deliveryCost = $deliveryCost;
    }

    protected static function getDistance($request, ?DeliveryMethod $deliveryMethod): ?float
    {
        if ($deliveryMethod === DeliveryMethod::DELIVERY_TO_ADDRESS) {
            $geoService = app(GeoService::class);

            $restaurantAddress = $geoService->getCoordsForRestaurant(Restaurant::getCurrentRestaurant());
            $clientAddress = Address::createFromRequest($request);
            $clientAddress = $geoService->getCoordsForValueObjectAddress($clientAddress);

            return number_format($geoService->calculateDistance($clientAddress, $restaurantAddress, true), 3, '.', '');
        }

        return null;
    }

    protected static function getPointsValue(?float $points): float
    {
        if ($points === null) {
            return 0;
        }

        $ratio = config(self::POINTS_RATIO_CONFIG_PATH);

        return round($points / $ratio, 2);
    }
}
