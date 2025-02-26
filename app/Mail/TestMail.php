<?php

namespace App\Mail;

use Asahasrabuddhe\LaravelMJML\Mail\Mailable;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\HtmlString;

class TestMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct()
    {
        //
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Test Mail',
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
            'title' => 'Potwierdzenie zamówienia',
            'user_name' => 'Arek Bogonos',
            'description' => 'Twoje zamówienie zostało zatwierdzone przez restauracje. Numer zamówienia RTJ29PK, 11:45 10.01.2024r.',
            'orders' => [
                [
                    'name' => 'Kebab mały',
                    'additions' => 'Mięso: mieszane | Sos: łagodny',
                    'price' => '18.00',
                    'currency' => 'PLN',
                ],
                [
                    'name' => 'Kebab pita',
                    'additions' => 'Mięso: kurczak | Sos: ostry | Komentarz: Bez kapusty',
                    'price' => '24.00',
                    'currency' => 'PLN',
                ],
                [
                    'name' => 'Frytki z posypką',
                    'additions' => 'Mięso: wołowe | Sos: mieszany',
                    'price' => '32.00',
                    'currency' => 'PLN',
                ],
                [
                    'name' => 'Frytki z posypką',
                    'additions' => 'Mięso: wołowe | Sos: mieszany',
                    'price' => '32.00',
                    'currency' => 'PLN',
                ],
                [
                    'name' => 'Frytki z posypką',
                    'additions' => 'Mięso: wołowe | Sos: mieszany',
                    'price' => '32.00',
                    'currency' => 'PLN',
                ],
                [
                    'name' => 'Frytki z posypką',
                    'additions' => 'Mięso: wołowe | Sos: mieszany',
                    'price' => '32.00',
                    'currency' => 'PLN',
                ],
                [
                    'name' => 'Frytki z posypką',
                    'additions' => 'Mięso: wołowe | Sos: mieszany',
                    'price' => '32.00',
                    'currency' => 'PLN',
                ],
                [
                    'name' => 'Frytki z posypką',
                    'additions' => 'Mięso: wołowe | Sos: mieszany',
                    'price' => '32.00',
                    'currency' => 'PLN',
                ],
                [
                    'name' => 'Frytki z posypką',
                    'additions' => 'Mięso: wołowe | Sos: mieszany',
                    'price' => '32.00',
                    'currency' => 'PLN',
                ],
                [
                    'name' => 'Frytki z posypką',
                    'additions' => 'Mięso: wołowe | Sos: mieszany',
                    'price' => '32.00',
                    'currency' => 'PLN',
                ],
                [
                    'name' => 'Frytki z posypką',
                    'additions' => 'Mięso: wołowe | Sos: mieszany',
                    'price' => '32.00',
                    'currency' => 'PLN',
                ],
                [
                    'name' => 'Frytki z posypką',
                    'additions' => 'Mięso: wołowe | Sos: mieszany',
                    'price' => '32.00',
                    'currency' => 'PLN',
                ],
                [
                    'name' => 'Frytki z posypką',
                    'additions' => 'Mięso: wołowe | Sos: mieszany',
                    'price' => '32.00',
                    'currency' => 'PLN',
                ],
                [
                    'name' => 'Frytki z posypką',
                    'additions' => 'Mięso: wołowe | Sos: mieszany',
                    'price' => '32.00',
                    'currency' => 'PLN',
                ],
                [
                    'name' => 'Frytki z posypką',
                    'additions' => 'Mięso: wołowe | Sos: mieszany',
                    'price' => '32.00',
                    'currency' => 'PLN',
                ],
                [
                    'name' => 'Frytki z posypką',
                    'additions' => 'Mięso: wołowe | Sos: mieszany',
                    'price' => '32.00',
                    'currency' => 'PLN',
                ],
                [
                    'name' => 'Frytki z posypką',
                    'additions' => 'Mięso: wołowe | Sos: mieszany',
                    'price' => '32.00',
                    'currency' => 'PLN',
                ],
            ],
            'prices' => [
                [
                    'name' => 'Dania',
                    'value' => '74.00',
                    'currency' => 'PLN',
                ],
                [
                    'name' => 'Opakowania',
                    'value' => '3.00',
                    'currency' => 'PLN',
                ],
                [
                    'name' => 'Dostawa',
                    'value' => '6.50',
                    'currency' => 'PLN',
                ],
                [
                    'name' => 'Opłata serwisowa',
                    'value' => '2.00',
                    'currency' => 'PLN',
                ],
                [
                    'name' => 'Wydane punkty',
                    'value' => '85.50',
                    'currency' => 'PLN',
                ],
            ],
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

        return $this->mjml('mail.order', $data)->buildMjmlView()['html'];
    }
}
