<?php

namespace App\Observers;

use App\Managers\RestaurantManager;
use App\Models\DeliveryRange;
use App\Models\Restaurant;
use App\Repositories\MultiTentantRepositoryTrait;
use Illuminate\Support\Facades\DB;
use phpseclib3\File\ASN1\Element;

class DeliveryRangeObserver
{
    use MultiTentantRepositoryTrait;

    /**
     * Handle the DeliveryRange "created" event.
     */
    public function created(DeliveryRange $deliveryRange): void
    {
        $lastDelivery = DeliveryRange::latest()->first();
        $restaurantId = Restaurant::getCurrentRestaurant()->id;
        $polygonString = $this->generatePolygonPoints($lastDelivery);

        DB::table('restaurants')->where('id', $restaurantId)->update([
            'max_delivery_range' => DB::raw("ST_GeomFromText('{$polygonString}')")
        ]);
    }

    /**
     * Handle the DeliveryRange "updated" event.
     */
    public function updated(DeliveryRange $deliveryRange): void
    {
    }

    /**
     * Handle the DeliveryRange "deleted" event.
     */
    public function deleted(DeliveryRange $deliveryRange): void
    {
        $lastDelivery = DeliveryRange::latest()->first();
        $restaurant = Restaurant::getCurrentRestaurant();
        $polygonString = $this->generatePolygonPoints($lastDelivery);
        if($polygonString!='POLYGON((0 0,0 0,0 0))'){
            DB::table('restaurants')->where('id', $restaurant->id)->update([
                'max_delivery_range' => DB::raw("ST_GeomFromText('{$polygonString}')")
            ]);
        }
        else {
            $manager = app(RestaurantManager::class);
            $manager->revertRestaurantDeliveryPolygonToDefault($restaurant);
        }

    }

    /**
     * Handle the DeliveryRange "restored" event.
     */
    public function restored(DeliveryRange $deliveryRange): void
    {
    }

    /**
     * Handle the DeliveryRange "force deleted" event.
     */
    public function forceDeleted(DeliveryRange $deliveryRange): void
    {
    }

    private function generatePolygonPoints(?DeliveryRange $lastDelivery): string
    {
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

        return $polygonString;
    }
}
