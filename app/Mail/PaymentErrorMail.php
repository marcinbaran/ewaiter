<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PaymentErrorMail extends Mailable
{
    use Queueable, SerializesModels;

    public $payment;

    public $error;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($payment, $error = '')
    {
        $this->payment = $payment;
        $this->error = $error;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('Problem z płatnością')->view('admin.emails.payment_error');
    }
}
