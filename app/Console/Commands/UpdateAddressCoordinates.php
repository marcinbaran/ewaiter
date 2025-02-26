<?php

namespace App\Console\Commands;

use App\Models\Address;
use App\Services\GeoServices\GeoService;
use Illuminate\Console\Command;

class UpdateAddressCoordinates extends Command
{
    protected $signature = 'addresses:update-coordinates';

    protected $description = 'Update addresses coordinates';

    public function __construct(private GeoService $geoService)
    {
        parent::__construct();
    }

    public function handle()
    {
        $addressesToUpdate = Address::whereNull('lat')
            ->orWhereNull('lng')
            ->get();

        foreach ($addressesToUpdate as $address) {
            $this->updateAddressCoordinates($address);
        }
    }

    private function updateAddressCoordinates(Address $address): void
    {
        $addressString = $this->getAddressString($address);
        $coordinates = $this->geoService->getCoords($addressString);
        $address->lat = $coordinates->getLat();
        $address->lng = $coordinates->getLng();
        $address->save();
    }

    private function getAddressString(Address $restaurantAddress): string
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
}
