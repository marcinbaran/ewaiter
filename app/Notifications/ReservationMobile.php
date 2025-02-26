<?php

namespace App\Notifications;

use App\Models\PlayerId;
use App\Models\Reservation;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notification;

class ReservationMobile extends Notification implements NotificationInterface
{
    use Queueable,
        NotificationTrait;

    /**
     * @var int
     */
    public $object_id;

    /**
     * @var string
     */
    public $object;

    /**
     * @var string
     */
    public $description = 'The reservation has been processed';

    /**
     * @param Reservation $reservation
     *
     * @return Reservation
     */
    public function getNotifiable($reservation)
    {
        throw_if(! ($reservation instanceof Reservation), new \Exception('The wrong object was given. It\'s a system notification, it can not be created by a manager or a table.', 400));

        $this->object = 'reservations';
        $this->object_id = $reservation->id;
        $this->description = $reservation->active ? __('firebase.The reservation has been confirmed') : __('firebase.The reservation has been rejected');

        return $reservation;
    }

    /**
     * @param Reservation $notifiable
     *
     * @return Collection
     */
    public function getDevice($notifiable)
    {
        return PlayerId::findDevicesByRoles([User::ROLE_MANAGER, User::ROLE_ADMIN, User::ROLE_WAITER]);
    }

    /**
     * @param Model $reservation
     *
     * @return array
     */
    public function getPushData($reservation): array
    {
        return [];
    }
}
