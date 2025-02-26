<?php

namespace App\Notifications;

use App\Models\PlayerId;
use App\Models\Room;
use App\Models\Table;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Notifications\Notification;

class Waiter extends Notification implements NotificationInterface
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
    public $description = 'The table call waiter';

    /**
     * @param Table|User $notifiable
     *
     * @return Table
     *
     * @throws \Exception
     */
    public function getNotifiable($model)
    {
        if ($model instanceof User) {
            $model = $model->table;

            return $model;
        }
        if ($model instanceof Table) {
            $this->description = __('admin.The table call waiter').'. '.__('admin.Number').': '.$model->number;

            return $model;
        }
        if ($model instanceof Room) {
            $this->description = __('admin.The room call waiter').'. '.__('admin.Number').': '.$model->number;

            return $model;
        }
        throw new \Exception('The wrong object was given. You do not have a table assigned and you must attach table ID.', 400);
    }

    /**
     * @param Table $notifiable
     *
     * @return Collection
     */
    public function getDevice($notifiable)
    { // call waiter - send push messages to managers
        return PlayerId::findDevicesByRoles([User::ROLE_WAITER, User::ROLE_ADMIN, User::ROLE_MANAGER]);
    }

    /**
     * @param Table $table
     *
     * @return array
     */
    public function getPushData($table)
    {
        return [];
        throw_if(! ($table instanceof Table), new \Exception('The wrong object was given.', 400));

        return [
            'table' => [
                'id' => $table->id,
                'name' => $table->name,
            ],
        ];
    }
}
