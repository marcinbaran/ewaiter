<?php

namespace App\Notifications;

use App\Models\Order;
use App\Models\PlayerId;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notification;

class StatusOrder extends Notification implements NotificationInterface
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
    public $description = 'The order changed its status';

    /**
     * @param Order $order
     *
     * @return Order
     */
    public function getNotifiable($order)
    {
        throw_if(! ($order instanceof Order), new \Exception('The wrong object was given. It\'s a system notification, it can not be created by a manager or a table.', 400));

        $this->object = 'orders';
        $this->object_id = $order->id;
        $this->description = __('admin.The order changed its status');

        return $order;
    }

    /**
     * @param Order $notifiable
     *
     * @return Collection
     */
    public function getDevice($notifiable)
    {
        return PlayerId::findDevicesByRoles([User::ROLE_MANAGER, User::ROLE_ADMIN, User::ROLE_WAITER]);
    }

    /**
     * @param Model $order
     *
     * @return array
     */
    public function getPushData($order): array
    {
        return [];
    }
}
