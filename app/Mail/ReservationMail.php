<?php

namespace App\Mail;

use Asahasrabuddhe\LaravelMJML\Mail\Mailable;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ReservationMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct( private readonly array $validatedData)
    {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->validatedData['subject'],
        );
    }

    public function content(): Content
    {
        return new Content(view: $this->getView());
    }


    public function getView(): string
    {
        $data = [
            'website_title' => 'E-Waiter Rezerwacje',
            'title' => $this->validatedData['title'],
            'user_name' => $this->validatedData['name'],
            'description' => $this->validatedData['message'],
            'buttons' => [
                [
                    'text' => 'Wróć do sklepu',
                    'url' => 'https://www.e-waiter.pl/',
                    'color' => 'white',
                ],
                [
                    'text' => 'Zainstaluj aplikację',
                    'url' => 'https://www.e-waiter.pl/',
                    'color' => 'light-pink',
                ],
            ],
            'greeting' => $this->validatedData['greeting'],
        ];

        return $this->mjml('mail.reservation', $data)->buildMjmlView()['html'];

    }
    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view($this->getView())
            ->subject($this->validatedData['subject'])
            ->from(env('RESERVATION_MAIL_USERNAME','no-reply@e-waiter.pl'), __('emails.ewaiter'));

    }
}
