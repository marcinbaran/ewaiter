<?php

namespace App\Listeners;

use App\Events\ReservationMobileEvent;

class ReservationMobileListener
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
     * @param ReservationMobileEvent $event
     */
    public function handle(ReservationMobileEvent $event)
    {
        $event->notificationMobileSent();
    }
}
