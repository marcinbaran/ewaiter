<?php

namespace App\Observers;

use App\Events\ChangeLogs;
use App\Models\Refund;

class RefundObserver
{
    /**
     * @param Refund $model
     */
    public function creating(Refund $model)
    {
    }

    /**
     * @param Refund $model
     */
    public function created(Refund $model)
    {
        event(new ChangeLogs($model, 'created'));
    }

    /**
     * @param Refund $model
     */
    public function updating(Refund $model)
    {
    }

    /**
     * @param Refund $model
     */
    public function updated(Refund $model)
    {
        event(new ChangeLogs($model, 'updated'));
    }

    /**
     * @param Refund $model
     */
    public function saving(Refund $model)
    {
    }

    /**
     * @param Refund $model
     */
    public function saved(Refund $model)
    {
    }

    /**
     * @param Refund $model
     */
    public function deleting(Refund $model)
    {
    }

    /**
     * @param Refund $model
     */
    public function deleted(Refund $model)
    {
        event(new ChangeLogs($model, 'deleted'));
    }

    /**
     * @param Refund $model
     */
    public function restoring(Refund $model)
    {
    }

    /**
     * @param Refund $model
     */
    public function restored(Refund $model)
    {
    }

    /**
     * @param Refund $model
     */
    public function retrieved(Refund $model)
    {
    }
}
