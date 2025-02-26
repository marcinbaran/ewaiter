<?php

namespace App\Listeners;

use App\Events\BillStatusEvent;

class BillStatusListener
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
     * @param BillStatusEvent $event
     */
    public function handle(BillStatusEvent $event)
    {
        $event->notificationSent();
    }
}
