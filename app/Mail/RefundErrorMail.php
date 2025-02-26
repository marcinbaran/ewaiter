<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class RefundErrorMail extends Mailable
{
    use Queueable, SerializesModels;

    public $refund;

    public $error;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($refund, $error = '')
    {
        $this->refund = $refund;
        $this->error = $error;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('Problem ze zwrotem płatności')->view('admin.emails.refund_error');
    }
}
