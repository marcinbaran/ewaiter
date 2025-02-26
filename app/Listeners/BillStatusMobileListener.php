<?php

namespace App\Listeners;

use App\Events\BillStatusMobileEvent;

class BillStatusMobileListener
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
     * @param BillStatusMobileEvent $event
     */
    public function handle(BillStatusMobileEvent $event)
    {
        $event->notificationMobileSent();
    }
}
