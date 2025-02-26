<?php

namespace App\Events\Orders;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class OrderEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    private $eventName;


    /**
     * Create a new Friend event instance.
     */
    public function __construct(private $order, string $eventName)
    {
        $this->eventName = $eventName;
    }

    public function broadcastOn(): array
    {
        return [new Channel('orderEvents')];
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastAs(): string
    {
        return "order.".$this->eventName;
    }


    public function broadcastWith()
    {
        return [
            'order' => $this->order,
        ];
    }
}
