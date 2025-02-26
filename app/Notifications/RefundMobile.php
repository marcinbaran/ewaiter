<?php

namespace App\Notifications;

use App\Models\PlayerId;
use App\Models\Refund;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notification;

class RefundMobile extends Notification implements NotificationInterface
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
    public $description = 'The refund has been processed';

    /**
     * @param Refund $refund
     *
     * @return Refund
     */
    public function getNotifiable($refund)
    {
        throw_if(! ($refund instanceof Refund), new \Exception('The wrong object was given. It\'s a system notification, it can not be created by a manager or a table.', 400));

        $this->object = 'refunds';
        $this->object_id = $refund->id;
        $this->description = __('firebase.The refund has been processed');

        return $refund;
    }

    /**
     * @param Refund $notifiable
     *
     * @return Collection
     */
    public function getDevice($notifiable)
    {
        return PlayerId::findDevicesByRoles([User::ROLE_MANAGER, User::ROLE_ADMIN, User::ROLE_WAITER]);
    }

    /**
     * @param Model $refund
     *
     * @return array
     */
    public function getPushData($refund): array
    {
        return [];
    }
}
