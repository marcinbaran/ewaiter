<?php

namespace App\Console\Commands;

use App\DTO\PointMapCoordinatesDTO;
use App\Models\Restaurant;
use App\Services\GeoServices\GoogleService;
use Illuminate\Console\Command;

class UpdateRestaurantsCoordinates extends Command
{
    protected $signature = 'restaurants:update-coordinates';

    protected $description = 'Update restaurant coordinates';

    public function __construct(private GoogleService $googleService)
    {
        parent::__construct();
    }

    public function handle()
    {
        $restaurants = Restaurant::all();

        try {
            foreach ($restaurants as $restaurant) {
                $restaurantAddress = $restaurant->address()->first();
                $currentAddressString = $this->getAddressString($restaurantAddress);

                $newCoordinates = $this->googleService->getCoords($currentAddressString);

                $this->saveNewCoordinates($newCoordinates, $restaurantAddress);
            }
        } catch (\Throwable $e) {
            return;
        }
    }

    public function getAddressString($restaurantAddress): string
    {
        $addressString = sprintf(
            '%s %s %s %s',
            $restaurantAddress->street,
            $restaurantAddress->building_number,
            str_replace('-', '', $restaurantAddress->postcode),
            $restaurantAddress->city
        );

        return $addressString;
    }

    public function saveNewCoordinates(PointMapCoordinatesDTO $newCoordinates, $restaurantAddress): void
    {
        $restaurantAddress->lat = $newCoordinates->getLat();
        $restaurantAddress->lng = $newCoordinates->getLng();
        $restaurantAddress->save();
    }
}
