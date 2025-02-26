<?php

namespace App\Notifications;

use App\Models\Bill;
use App\Models\PlayerId;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notification;

class StatusBill extends Notification implements NotificationInterface
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
     * @param Bill $bill
     *
     * @return Bill
     */
    public function getNotifiable($bill)
    {
        throw_if(! ($bill instanceof Bill), new \Exception('The wrong object was given. It\'s a system notification, it can not be created by a manager or a table.', 400));

        $this->object = 'bills';
        $this->object_id = $bill->id;
        $this->description = __('admin.The order changed its status');

        return $bill;
    }

    /**
     * @param Bill $notifiable
     *
     * @return Collection
     */
    public function getDevice($notifiable)
    {
        return PlayerId::findDevicesByRoles([User::ROLE_MANAGER, User::ROLE_ADMIN, User::ROLE_WAITER]);
    }

    /**
     * @param Model $bill
     *
     * @return array
     */
    public function getPushData($bill): array
    {
        /*
         * TODO
         */
        return [];
        $table = $bill->orders->first()->table;

        return [
            'type' => Bill::getStatusName($bill->status).'_bill',
            'bill' => [
                'id' => $bill->id,
                'status' => $bill->status,
                'table' => [
                    'id' => $table->id,
                    'name' => $table->name,
                ],
            ],
        ];
    }
}
