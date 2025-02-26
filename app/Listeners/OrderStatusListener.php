<?php

namespace App\Listeners;

use App\Events\OrderStatusEvent;

class OrderStatusListener
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
    }

    /**
     * Handle the event.
     *
     * @param OrderStatusEvent $event
     */
    public function handle(OrderStatusEvent $event)
    {
        $event->notificationSent();
    }
}
