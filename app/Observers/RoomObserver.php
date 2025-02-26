<?php

namespace App\Observers;

use App\Events\ChangeLogs;
use App\Models\Room;

class RoomObserver
{
    /**
     * @param Room $model
     */
    public function creating(Room $model)
    {
    }

    /**
     * @param Room $model
     */
    public function created(Room $model)
    {
        event(new ChangeLogs($model, 'created'));
    }

    /**
     * @param Room $model
     */
    public function updating(Room $model)
    {
    }

    /**
     * @param Room $model
     */
    public function updated(Room $model)
    {
        event(new ChangeLogs($model, 'updated'));
    }

    /**
     * @param Room $model
     */
    public function saving(Room $model)
    {
    }

    /**
     * @param Room $model
     */
    public function saved(Room $model)
    {
    }

    /**
     * @param Room $model
     */
    public function deleting(Room $model)
    {
    }

    /**
     * @param Room $model
     */
    public function deleted(Room $model)
    {
        event(new ChangeLogs($model, 'deleted'));
    }

    /**
     * @param Room $model
     */
    public function restoring(Room $model)
    {
    }

    /**
     * @param Room $model
     */
    public function restored(Room $model)
    {
    }

    /**
     * @param Room $model
     */
    public function retrieved(Room $model)
    {
    }
}
