<?php

namespace App\Observers;

use App\Events\ChangeLogs;
use App\Models\Notification;

class NotificationObserver
{
    /**
     * @param Notification $model
     */
    public function creating(Notification $model)
    {
    }

    /**
     * @param Notification $model
     */
    public function created(Notification $model)
    {
        event(new ChangeLogs($model, 'created'));
    }

    /**
     * @param Notification $model
     */
    public function updating(Notification $model)
    {
    }

    /**
     * @param Notification $model
     */
    public function updated(Notification $model)
    {
        event(new ChangeLogs($model, 'updated'));
    }

    /**
     * @param Notification $model
     */
    public function saving(Notification $model)
    {
    }

    /**
     * @param Notification $model
     */
    public function saved(Notification $model)
    {
    }

    /**
     * @param Notification $model
     */
    public function deleting(Notification $model)
    {
    }

    /**
     * @param Notification $model
     */
    public function deleted(Notification $model)
    {
        event(new ChangeLogs($model, 'deleted'));
    }

    /**
     * @param Notification $model
     */
    public function restoring(Notification $model)
    {
    }

    /**
     * @param Notification $model
     */
    public function restored(Notification $model)
    {
    }

    /**
     * @param Notification $model
     */
    public function retrieved(Notification $model)
    {
    }
}
