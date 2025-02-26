<?php

namespace App\Observers;

use App\Events\ChangeLogs;
use App\Models\Addition;

class AdditionObserver
{
    /**
     * @param Addition $model
     */
    public function creating(Addition $model)
    {
    }

    /**
     * @param Addition $model
     */
    public function created(Addition $model)
    {
        event(new ChangeLogs($model, 'created'));
    }

    /**
     * @param Addition $model
     */
    public function updating(Addition $model)
    {
    }

    /**
     * @param Addition $model
     */
    public function updated(Addition $model)
    {
        event(new ChangeLogs($model, 'updated'));
    }

    /**
     * @param Addition $model
     */
    public function saving(Addition $model)
    {
    }

    /**
     * @param Addition $model
     */
    public function saved(Addition $model)
    {
    }

    /**
     * @param Addition $model
     */
    public function deleting(Addition $model)
    {
    }

    /**
     * @param Addition $model
     */
    public function deleted(Addition $model)
    {
        event(new ChangeLogs($model, 'deleted'));
    }

    /**
     * @param Addition $model
     */
    public function restoring(Addition $model)
    {
    }

    /**
     * @param Addition $model
     */
    public function restored(Addition $model)
    {
    }

    /**
     * @param Addition $model
     */
    public function retrieved(Addition $model)
    {
    }
}
