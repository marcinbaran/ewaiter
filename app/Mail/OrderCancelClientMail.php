<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class OrderCancelClientMail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;

    public $bill;

    public $restaurant;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($user, $bill, $restaurant)
    {
        $this->user = $user;
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
        return $this->subject(gtrans('emails.Order has been canceled'))->view('admin.emails.order_cancel_client');
    }
}
