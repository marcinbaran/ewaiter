<?php

namespace App\Console\Commands;

use App\Events\MainScreen\ClosestRestaurants;
use App\Events\MainScreen\LatestRestaurants;
use App\Events\MainScreen\MostPopularRestaurants;
use App\Events\Restaurants\RestaurantsList;
use App\Models\Restaurant;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class BroadcastEvents extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'broadcast:events';


    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Broadcast events';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        try {
            $restaurants = Restaurant::all();
            broadcast(new RestaurantsList($restaurants));
        }
        catch (\Exception $e) {
            Log::error('Error in Broadcasting RestaurantsList: ' . $e->getMessage());
        }
    }
}
