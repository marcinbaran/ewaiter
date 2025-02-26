<?php

namespace App\Mail;

use Asahasrabuddhe\LaravelMJML\Mail\Mailable;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\HtmlString;

class ContactFormMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(private readonly array $validatedData,)
    { }

    public function envelope(): Envelope
    {
        return new Envelope(
            from: $this->validatedData['email'],
            subject: $this->validatedData['title'],
        );
    }

    public function content(): Content
    {
        return new Content(view: $this->getView());
    }

    public function attachments(): array
    {
        return [];
    }

    public function getView(): HtmlString
    {
        $data = [
            'website_title' => 'E-Waiter testowy',
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
}
