<?php

namespace App\Console\Commands;

use App\Models\DeliveryRange;
use App\Models\Restaurant;
use App\Repositories\MultiTentantRepositoryTrait;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class SetMaxDeliveryCoords extends Command
{
    use MultiTentantRepositoryTrait;

    protected $signature = 'app:set-max-delivery-coords';

    protected $description = 'Set max delivery coords for restaurants';

    public function handle()
    {
        try {
            $visibleRestaurants = Restaurant::visible()->get();

            foreach ($visibleRestaurants as $visibleRestaurant) {
                if (!$visibleRestaurant->address) continue;
                $this->updateRestaurantDeliveryPolygon($visibleRestaurant);
                $this->info('Updated max delivery polygon for restaurant ' . $visibleRestaurant->name);
            }
        } catch (\Exception $e) {
            $this->error('Error while getting visible restaurants');
            return;
        }
    }

    private function updateRestaurantDeliveryPolygon(
        Restaurant $visibleRestaurant
    ): void
    {
        $restaurantId = $visibleRestaurant->id;
        $this->reconnect($visibleRestaurant);
        $lastDelivery = DeliveryRange::latest()->first();

        if (!$lastDelivery) {
            $points = '[[0,0],[0,0]]';
        } else {
            $points = $lastDelivery->range_polygon;
        }

        $points = json_decode($points, true);

        $points[] = $points[0];
        $polygonString = 'POLYGON((' . implode(',', array_map(function ($point) {
                return $point[0] . ' ' . $point[1];
            }, $points)) . '))';

        $this->reset();
        DB::table('restaurants')->where('id', $restaurantId)->update([
            'max_delivery_range' => DB::raw("ST_GeomFromText('{$polygonString}')")
        ]);
    }
}
