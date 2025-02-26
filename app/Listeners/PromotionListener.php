<?php

namespace App\Listeners;

use App\Events\PromotionInterface;

class PromotionListener
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
     * @param PromotionInterface $event
     */
    public function handle(PromotionInterface $event)
    {
        $event->calculate();
    }
}
