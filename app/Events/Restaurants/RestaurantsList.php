<?php

namespace App\Events\Restaurants;

use App\Models\Restaurant;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class RestaurantsList implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;
    public $restaurants;
    /**
     * Create a new event instance.
     */
    public function __construct($restaurants)
    {
        $this->restaurants = $restaurants;
    }


    /**
     * Get the channels the event should broadcast on.
     *
     * @return Channel
     */
    public function broadcastOn(): Channel
    {
        return new Channel('restaurants');
    }
    public function broadcastAs(): string
    {
        return 'restaurants.list';
    }

    public function broadcastWith()
    {
        return [
            'updatedRestaurants' =>  $this->restaurants
        ];
    }
}
