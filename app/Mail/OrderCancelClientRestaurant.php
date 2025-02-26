<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class OrderCancelClientRestaurant extends Mailable
{
    use Queueable, SerializesModels;

    public $bill;

    public $restaurant;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($bill, $restaurant)
    {
        $this->bill = $bill;
        $this->restaurant = $restaurant;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject(gtrans('emails.Order has been canceled'))->view('admin.emails.order_cancel_restaurant');
    }
}
