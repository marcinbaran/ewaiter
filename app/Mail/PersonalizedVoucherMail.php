<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PersonalizedVoucherMail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;

    public $voucher_code;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($user, $voucher_code)
    {
        $this->user = $user;
        $this->voucher_code = $voucher_code;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject(gtrans('emails.Voucher'))->view('admin.emails.personalized_voucher');
    }
}
