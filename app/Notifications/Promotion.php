<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notification;

class Promotion extends Notification implements NotificationInterface
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
    public $description = 'The table has promotion';

    /**
     * @param Model $user
     *
     * @return Model
     */
    public function getNotifiable($user)
    {
        $this->description = __('admin.The table has promotion');

        return $user;
    }

    /**
     * @param Model $notifiable
     *
     * @return Collection
     */
    public function getDevice($notifiable)
    {
        return $notifiable->playerIds;
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
