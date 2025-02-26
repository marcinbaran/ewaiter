<?php

namespace App\Events\MainScreen;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MostPopularRestaurants implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct($restaurants)
    {
        $this->restaurants = $restaurants;
    }

    public function broadcastOn()
    {
        return new Channel('main-screen');
    }

    public function broadcastAs()
    {
        return 'restaurants.most-popular';
    }

    public function broadcastWith()
    {
        return ['popularRestaurants' => $this->restaurants];
    }
}
