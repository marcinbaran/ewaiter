<?php

namespace App\Listeners;

use App\Events\RefundMobileEvent;

class RefundMobileListener
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
     * @param RefundMobileEvent $event
     */
    public function handle(RefundMobileEvent $event)
    {
        $event->notificationMobileSent();
    }
}
