<?php

namespace App\Notifications;

use Illuminate\Notifications\Messages\DatabaseMessage;

trait NotificationTrait
{
    public function __construct()
    {
        $this->id = $this->guidv4();
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param mixed $notifiable
     *
     * @return array
     */
    public function via($notifiable)
    {
        return ['database'];
    }

    /**
     * Get the database message representation of the notification.
     *
     * @param mixed $notifiable
     *
     * @return DatabaseMessage
     */
    public function toDatabase($notifiable)
    {
        $data = ['description' => $this->description];

        return new DatabaseMessage($data);
    }

    /**
     * @return string
     */
    private function guidv4(): string
    {
        if (true === function_exists('com_create_guid')) {
            return trim(com_create_guid(), '{}');
        }

        $data = openssl_random_pseudo_bytes(16);
        $data[6] = chr(ord($data[6]) & 0x0f | 0x40); // set version to 0100
        $data[8] = chr(ord($data[8]) & 0x3f | 0x80); // set bits 6-7 to 10

        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
    }
}
