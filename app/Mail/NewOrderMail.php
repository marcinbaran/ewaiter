<?php

namespace App\Mail;

use App\Models\Bill;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Support\HtmlString;

class NewOrderMail extends BaseMail
{
    protected Bill $bill;

    public function __construct(Bill $bill)
    {
        parent::__construct();
        $this->bill = $bill;
    }

    public function content(): Content
    {
        $data = [
            'website_title' => 'Potwierdzenie zamówienia',
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

        return $this->getContent('mail.order', $data);
    }

    public function envelope(): Envelope
    {
        return $this->getEnvelope('Potwierdzenie zamówienia');
    }

    public function getView(): HtmlString
    {
        return $this->mjml('mail.order', $data)->buildMjmlView()['html'];
    }
}
