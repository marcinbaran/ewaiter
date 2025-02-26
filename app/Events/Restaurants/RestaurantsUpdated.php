<?php

namespace App\Events\Restaurants;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class RestaurantsUpdated  implements ShouldBroadcast
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
    public function broadcastOn()
    {
        return new Channel('restaurants');
    }

    public function broadcastAs(): string
    {
        return 'restaurants.updated';
    }

    public function broadcastWith(): array
    {
        return [
            'updatedRestaurants' => $this->restaurants
        ];
    }
}
