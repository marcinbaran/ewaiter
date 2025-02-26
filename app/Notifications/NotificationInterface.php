<?php

namespace App\Notifications;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

interface NotificationInterface
{
    /**
     * @param Model $model
     *
     * @return Model
     */
    public function getNotifiable($model);

    /**
     * @param Model $model
     *
     * @return Collection
     */
    public function getDevice($model);

    /**
     * @param Model $model
     *
     * @return array
     */
    public function getPushData($model);
}
