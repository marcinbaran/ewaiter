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

class UpdateRestaurantMinimalDeliveryPrice implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, MultiTentantRepositoryTrait;

    public function handle()
    {
        $restaurants = Restaurant::all();

        foreach ($restaurants as $restaurant) {
            $this->reconnect($restaurant);
            $minimalDeliveryPrice = DeliveryRange::orderBy('cost', 'asc')->value('cost');
            $this->reset();

            $restaurant->minimal_delivery_price = $minimalDeliveryPrice;
            $restaurant->save();
        }
    }

}
