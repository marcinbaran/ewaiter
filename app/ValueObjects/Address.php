<?php

namespace App\ValueObjects;

use App\Http\Requests\Api\PlaceOrderRequest;
use App\Models\Restaurant;
use App\Services\GeoServices\GeoService;
use Illuminate\Http\Request;

final class Address
{
    public function __construct(
        private ?string $city = null,
        private ?string $street = null,
        private ?string $houseNumber = null,
        private ?string $postalCode = null,
        private ?string $apartmentNumber = null,
        private ?string $country = null,
        private ?float $latitude = null,
        private ?float $longitude = null,
    ) {
    }

    public static function createFromRestaurant(Restaurant $restaurant, bool $withLatLng = false): self
    {
        $address = new self(
            ! empty($restaurant->address_system->city) ? $restaurant->address_system->city : null,
            ! empty($restaurant->address_system->street) ? $restaurant->address_system->street : null,
            ! empty($restaurant->address_system->building_number) ? $restaurant->address_system->building_number : null,
            ! empty($restaurant->address_system->postcode) ? $restaurant->address_system->postcode : null,
            ! empty($restaurant->address_system->house_number) ? $restaurant->address_system->house_number : null,
            null,
        );

        if ($withLatLng) {
            self::appendLatLng($address);
        }

        return $address;
    }

    public static function createFromRequest(Request $request, bool $withLatLng = false): self
    {
        return self::createFromArray($request->get(PlaceOrderRequest::DELIVERY_PARAM_KEY)['value'], $withLatLng);
    }

    public static function createFromArray(array $address, bool $withLatLng = false): self
    {
        $addressObject = new self(
            ! empty($address['city']) ? $address['city'] : null,
            ! empty($address['street']) ? $address['street'] : null,
            ! empty($address['building_number']) ? $address['building_number'] : null,
            ! empty($address['postcode']) ? $address['postcode'] : null,
            ! empty($address['apartment_number']) ? $address['apartment_number'] : null,
            ! empty($address['country']) ? $address['country'] : null,
            ! empty($address['latitude']) ? $address['latitude'] : null,
            ! empty($address['longitude']) ? $address['longitude'] : null,
        );

        if ($withLatLng && empty($address['latitude']) && empty($address['longitude'])) {
            self::appendLatLng($addressObject);
        }

        return $addressObject;
    }

    public function toArray(): array
    {
        return [
            'city' => $this->city,
            'postalCode' => $this->postalCode,
            'street' => $this->street,
            'houseNumber' => $this->houseNumber,
            'apartmentNumber' => $this->apartmentNumber,
            'country' => $this->country,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
        ];
    }

    public function toJson(): string
    {
        return json_encode($this->toArray());
    }

    public function getFullAddress(): string
    {
        $address = $this->toArray();

        unset($address['latitude']);
        unset($address['longitude']);

        foreach ($address as $key => $value) {
            if (empty($value)) {
                unset($address[$key]);
            }
        }

        return implode(' ', array_filter($this->toArray()));
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function getStreet(): ?string
    {
        return $this->street;
    }

    public function getPostalCode(): ?string
    {
        return $this->postalCode;
    }

    public function getHouseNumber(): ?string
    {
        return $this->houseNumber;
    }

    public function getApartmentNumber(): ?string
    {
        return $this->apartmentNumber;
    }

    public function getCountry(): ?string
    {
        return $this->country;
    }

    public function getLatitude(): ?string
    {
        return $this->latitude;
    }

    private static function appendLatLng(self $address): void
    {
        $geoService = app(GeoService::class);
        $location = $geoService->getCoordsForValueObjectAddress($address);

        $address->setLatitude($location['lat']);
        $address->setLongitude($location['lng']);
    }

    public function getLongitude(): ?string
    {
        return $this->longitude;
    }

    private function setLatitude(string $latitude): void
    {
        $this->latitude = $latitude;
    }

    private function setLongitude(string $longitude): void
    {
        $this->longitude = $longitude;
    }
}
