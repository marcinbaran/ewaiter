<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class WithdrawErrorMail extends Mailable
{
    use Queueable, SerializesModels;

    public $ids;

    public $error;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($ids, $error = '')
    {
        $this->ids = $ids;
        $this->error = $error;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('Problem z przelewem masowym')->view('admin.emails.withdraw_error');
    }
}
