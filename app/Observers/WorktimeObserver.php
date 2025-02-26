<?php

namespace App\Observers;

use App\Events\ChangeLogs;
use App\Models\Worktime;

class WorktimeObserver
{
    /**
     * @param Worktime $model
     */
    public function creating(Worktime $model)
    {
    }

    /**
     * @param Worktime $model
     */
    public function created(Worktime $model)
    {
        event(new ChangeLogs($model, 'created'));
    }

    /**
     * @param Worktime $model
     */
    public function updating(Worktime $model)
    {
    }

    /**
     * @param Worktime $model
     */
    public function updated(Worktime $model)
    {
        event(new ChangeLogs($model, 'updated'));
    }

    /**
     * @param Worktime $model
     */
    public function saving(Worktime $model)
    {
    }

    /**
     * @param Worktime $model
     */
    public function saved(Worktime $model)
    {
    }

    /**
     * @param Worktime $model
     */
    public function deleting(Worktime $model)
    {
    }

    /**
     * @param Worktime $model
     */
    public function deleted(Worktime $model)
    {
        event(new ChangeLogs($model, 'deleted'));
    }

    /**
     * @param Worktime $model
     */
    public function restoring(Worktime $model)
    {
    }

    /**
     * @param Worktime $model
     */
    public function restored(Worktime $model)
    {
    }

    /**
     * @param Worktime $model
     */
    public function retrieved(Worktime $model)
    {
    }
}
