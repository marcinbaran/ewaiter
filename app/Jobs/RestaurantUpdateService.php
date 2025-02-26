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

class RestaurantUpdateService implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, MultiTentantRepositoryTrait;

    public function handle()
    {
        $visibleRestaurants = Restaurant::visible()->get();

        foreach ($visibleRestaurants as $visibleRestaurant) {
            $this->reconnect($visibleRestaurant);
            $dr = DeliveryRange::orderBy('min_value', 'asc')->first();
            if ($dr) {
                (new \App\Services\RestaurantUpdateService($visibleRestaurant, $dr))->updateRestaurant();
            }
        }
    }
}
