<?php

namespace App\Observers;

use App\Events\ChangeLogs;
use App\Models\Table;

class TableObserver
{
    /**
     * @param Table $model
     */
    public function creating(Table $model)
    {
    }

    /**
     * @param Table $model
     */
    public function created(Table $model)
    {
        event(new ChangeLogs($model, 'created'));
    }

    /**
     * @param Table $model
     */
    public function updating(Table $model)
    {
    }

    /**
     * @param Table $model
     */
    public function updated(Table $model)
    {
        event(new ChangeLogs($model, 'updated'));
    }

    /**
     * @param Table $model
     */
    public function saving(Table $model)
    {
    }

    /**
     * @param Table $model
     */
    public function saved(Table $model)
    {
    }

    /**
     * @param Table $model
     */
    public function deleting(Table $model)
    {
    }

    /**
     * @param Table $model
     */
    public function deleted(Table $model)
    {
        event(new ChangeLogs($model, 'deleted'));
    }

    /**
     * @param Table $model
     */
    public function restoring(Table $model)
    {
    }

    /**
     * @param Table $model
     */
    public function restored(Table $model)
    {
    }

    /**
     * @param Table $model
     */
    public function retrieved(Table $model)
    {
    }
}
