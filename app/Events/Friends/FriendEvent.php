<?php

namespace App\Events\Friends;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class FriendEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    private $eventName;


    /**
     * Create a new Friend event instance.
     */
    public function __construct(private $reciverId, private $senderId, string $eventName)
    {
        $this->eventName = $eventName;
    }

    public function broadcastOn(): array
    {
        return [new Channel('friendEvents')];
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastAs(): string
    {
        return "friend.".$this->eventName;
    }


    public function broadcastWith()
    {
        return [
            'message' => 'Friend '.$this->eventName,
            'reciverId' => $this->reciverId,
            'senderId' => $this->senderId
        ];
    }
}
