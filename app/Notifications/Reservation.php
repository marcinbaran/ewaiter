<?php

namespace App\Notifications;

use App\Models\PlayerId;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notification;

class Reservation extends Notification implements NotificationInterface
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
    public $description = 'Awaiting reservation';

    /**
     * @param Model $reservation
     *
     * @return Model
     */
    public function getNotifiable($reservation)
    {
        $this->object = 'reservations';
        $this->object_id = $reservation->id;
        $this->description = __('admin.Awaiting reservation');

        return $reservation;
    }

    /**
     * @param Model $notifiable
     *
     * @return Collection
     */
    public function getDevice($notifiable)
    {
        return PlayerId::findDevicesByRoles([User::ROLE_WAITER, User::ROLE_ADMIN, User::ROLE_MANAGER]);
    }

    /**
     * @todo We do not use this notification yet
     *
     * @param Model $model
     *
     * @return array
     */
    public function getPushData($model): array
    {
        return [];
    }
}
