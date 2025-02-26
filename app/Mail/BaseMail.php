<?php

namespace App\Mail;

use Asahasrabuddhe\LaravelMJML\Mail\Mailable;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

abstract class BaseMail extends Mailable
{
    use Queueable, SerializesModels;

    protected string $appName;

    public function __construct()
    {
        $this->appName = config('app.name', 'E-Waiter');
    }

    public function envelope(): Envelope
    {
        return $this->getEnvelope();
    }

    public function content(): Content
    {
        return $this->getContent();
    }

    public function attachments(): array
    {
        return [];
    }

    public function getContent(string $viewName = 'mail.base', array $data = []): Content
    {
        return new Content(view: $this->mjml($viewName, $data)->buildMjmlView()['html']);
    }

    public function getEnvelope(string $subject = ''): Envelope
    {
        return new Envelope(
            subject: $this->appName.': '.$subject,
        );
    }
}
