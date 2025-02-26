<?php

namespace App\Console\Commands;

use App\Helpers\PolygonHelper;
use App\Models\AddressSystem;
use App\Models\DeliveryRange;
use App\Models\Restaurant;
use App\Repositories\MultiTentantRepositoryTrait;
use Exception;
use Illuminate\Console\Command;

class GeneratePolygonFromCircle extends Command
{
    use MultiTentantRepositoryTrait;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:generate-polygon-from-circle';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command will auto complete polygon field in delivery in all restaurants';

    /**
     * Execute the console command.
     * @throws Exception
     */
    public function handle()
    {
        $visibleRestaurants = Restaurant::visible()->get();

        foreach ($visibleRestaurants as $visibleRestaurant) {
            if (!$visibleRestaurant->address) continue;
            $this->updateRestaurantDeliveryPolygon($visibleRestaurant->address, $visibleRestaurant);
        }
    }

    /**
     * @throws Exception
     */
    private function updateRestaurantDeliveryPolygon(
        AddressSystem $restaurantAddress,
        Restaurant $visibleRestaurant
    ): void {
        $this->reconnect($visibleRestaurant);

        foreach (DeliveryRange::all() as $deliveryRange) {
            if (!$restaurantAddress->lat || !$restaurantAddress->lng) continue;
            $radius = $deliveryRange->range_to;
            $deliveryRange->range_polygon = PolygonHelper::getCirclePolygonCoordinates(
                $restaurantAddress->lat,
                $restaurantAddress->lng,
                $radius
            );
            $deliveryRange->save();
        }

        $this->reset();
    }
}
