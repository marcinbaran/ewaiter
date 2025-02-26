<?php

namespace App\Services;

use App\ValueObjects\Address;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GeolocationService
{
    protected const METERS_IN_KILOMETER = 1000;

//    public function getDistanceFromAddress(Address $fromAddress, Address $toAddress, bool $returnInKilometers = true): float
//    {
//        $distanceInMeters = app(GoogleDistanceService::class)->calculate($fromAddress->getFullAddress(), $toAddress->getFullAddress());
//
//        return $returnInKilometers ? self::getDistanceInKilometers($distanceInMeters) : $distanceInMeters;
//    }

    public function getDistanceFromAddress(Address $fromAddress, Address $toAddress, bool $returnInKilometers = true): float
    {
        $startCoords = implode(',', $this->getLongitudeAndLatitudeFromAddress($fromAddress));
        $endCoords = implode(',', $this->getLongitudeAndLatitudeFromAddress($toAddress));
        $url = "http://router.project-osrm.org/route/v1/driving/$startCoords;$endCoords?overview=false";

        $response = Http::get($url);
        $result = $response->json();

        if (! empty($result) && isset($result['routes'][0])) {
            $distanceInMeters = $result['routes'][0]['distance'];

            return $returnInKilometers ? self::getDistanceInKilometers($distanceInMeters) : $distanceInMeters;
        }

        return false;
    }

//    public function getLongitudeAndLatitudeFromAddress(Address $address): array
//    {
//        $response = GeolocationService::getCoordinatesForAddress($address->getFullAddress());
//
//        return [
//            'latitude' => $response['lat'],
//            'longitude' => $response['lng'],
//        ];
//    }

    public function getLongitudeAndLatitudeFromAddress(string|Address $address): array
    {
        $string = $address instanceof Address ? $address->getFullAddress() : $address;
        $url = 'https://nominatim.openstreetmap.org/search?format=json&limit=1&q='.urlencode($string);
        $result = Cache::rememberForever($url, function () use ($url) {
            $response = Http::get($url);

            return $response->json();
        });
        Log::info($url);

        if (! empty($result)) {
            return [
                'latitude' => $result[0]['lat'],
                'longitude' => $result[0]['lon'],
            ];
        } else {
            Log::error('Could not get coordinates from address: '.$string);
        }

        return [];
    }

    public function getDistanceInKilometers(int|float $distanceInMeters): float
    {
        return $distanceInMeters / self::METERS_IN_KILOMETER;
    }

    public static function getCoordinatesForAddress(string|Address $address)
    {
        $osmResponse = (new self())->getLongitudeAndLatitudeFromAddress($address);

        return [
            'lat' => $osmResponse['latitude'] ?? '',
            'lng' => $osmResponse['longitude'] ?? '',
        ];
    }
}
