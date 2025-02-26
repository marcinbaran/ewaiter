<?php

namespace App\DTO\Orders;

use App\Enum\DeliveryMethod;
use App\Http\Requests\Api\PlaceOrderRequest;

final class CreateAddressDTO
{
    private ?int $id = null;

    public function __construct(
        private readonly string  $name,
        private readonly string  $phone,
        private readonly string  $city,
        private readonly string  $postcode,
        private readonly string  $street,
        private readonly string  $buildingNumber,
        private readonly ?string $apartmentNumber = null,
    ) {}

    public static function createFromRequest(PlaceOrderRequest $request): ?self
    {
        if ($request->delivery['type'] !== DeliveryMethod::DELIVERY_TO_ADDRESS->value) {
            return null;
        }

        return new self(
            name: $request->delivery['value']['name'],
            phone: $request->delivery['value']['phone'],
            city: $request->delivery['value']['city'],
            postcode: $request->delivery['value']['postcode'],
            street: $request->delivery['value']['street'],
            buildingNumber: $request->delivery['value']['building_number'],
            apartmentNumber: $request->delivery['value']['house_number'],
        );
    }

    public function toArray()
    {
        return [
            'name' => $this->name,
            'phone' => $this->phone,
            'city' => $this->city,
            'postcode' => $this->postcode,
            'street' => $this->street,
            'building_number' => $this->buildingNumber,
            'house_number' => $this->apartmentNumber,
        ];
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getId(): int
    {
        return $this->id;
    }
}
