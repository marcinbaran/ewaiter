<?php

namespace App\Jobs;

use App\Models\DeliveryRange;
use App\Models\Restaurant;
use App\Repositories\MultiTentantRepositoryTrait;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SetMaxDeliveryCoords implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, MultiTentantRepositoryTrait;

    public function handle()
    {
        try {
            $visibleRestaurants = Restaurant::visible()->get();

            foreach ($visibleRestaurants as $visibleRestaurant) {
                if (!$visibleRestaurant->address) continue;
                $this->updateRestaurantDeliveryPolygon($visibleRestaurant);
                Log::info('CRON: Updated max delivery polygon for restaurant ' . $visibleRestaurant->name);
            }
        } catch (\Exception $e) {
            Log::error('CRON: Error while getting visible restaurants');
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
