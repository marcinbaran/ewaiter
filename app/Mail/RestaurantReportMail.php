<?php

namespace App\Mail;

use Asahasrabuddhe\LaravelMJML\Mail\Mailable;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\HtmlString;

class RestaurantReportMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(private readonly array $validatedData)
    { }

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

    public function attachments(): array
    {
        return [$this->validatedData['attachment']];
    }

    public function getView(): HtmlString
    {
        $data = [
            'website_title' => 'E-Waiter',
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
            'greeting' => null,
        ];

        return $this->mjml('mail.base', $data)->buildMjmlView()['html'];
    }

    public function build()
    {
        return $this->view($this->getView())
                    ->subject($this->validatedData['subject'])
                    ->cc(env('MAIL_FROM_ADDRESS', ''))
                    ->attach($this->validatedData['attachment'])
                    ->from(env('RAPORT_MAIL_USERNAME','rozliczenia@e-waiter.pl'), __('emails.ewaiter'));
    }
}
