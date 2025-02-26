<?php

namespace App\Observers;

use App\Events\ChangeLogs;
use App\Rating;

class RatingObserver
{
    /**
     * @param Rating $model
     */
    public function creating(Rating $model)
    {
    }

    /**
     * @param Rating $model
     */
    public function created(Rating $model)
    {
        event(new ChangeLogs($model, 'created'));
    }

    /**
     * @param Rating $model
     */
    public function updating(Rating $model)
    {
    }

    /**
     * @param Rating $model
     */
    public function updated(Rating $model)
    {
        event(new ChangeLogs($model, 'updated'));
    }

    /**
     * @param Rating $model
     */
    public function saving(Rating $model)
    {
    }

    /**
     * @param Rating $model
     */
    public function saved(Rating $model)
    {
    }

    /**
     * @param Rating $model
     */
    public function deleting(Rating $model)
    {
    }

    /**
     * @param Rating $model
     */
    public function deleted(Rating $model)
    {
        event(new ChangeLogs($model, 'deleted'));
    }

    /**
     * @param Rating $model
     */
    public function restoring(Rating $model)
    {
    }

    /**
     * @param Rating $model
     */
    public function restored(Rating $model)
    {
    }

    /**
     * @param Rating $model
     */
    public function retrieved(Rating $model)
    {
    }
}
