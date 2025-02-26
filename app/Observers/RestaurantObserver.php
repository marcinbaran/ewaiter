<?php

namespace App\Observers;

use App\Events\ChangeLogs;
use App\Models\Restaurant;

class RestaurantObserver
{
    /**
     * @param Restaurant $model
     */
    public function creating(Restaurant $model)
    {
    }

    /**
     * @param Restaurant $model
     */
    public function created(Restaurant $model)
    {
        //event(new ChangeLogs($model, 'created'));
    }

    /**
     * @param Restaurant $model
     */
    public function updating(Restaurant $model)
    {
    }

    /**
     * @param Restaurant $model
     */
    public function updated(Restaurant $model)
    {
        //event(new ChangeLogs($model, 'updated'));
    }

    /**
     * @param Restaurant $model
     */
    public function saving(Restaurant $model)
    {
    }

    /**
     * @param Restaurant $model
     */
    public function saved(Restaurant $model)
    {
    }

    /**
     * @param Restaurant $model
     */
    public function deleting(Restaurant $model)
    {
    }

    /**
     * @param Restaurant $model
     */
    public function deleted(Restaurant $model)
    {
        //event(new ChangeLogs($model, 'deleted'));
    }

    /**
     * @param Restaurant $model
     */
    public function restoring(Restaurant $model)
    {
    }

    /**
     * @param Restaurant $model
     */
    public function restored(Restaurant $model)
    {
    }

    /**
     * @param Restaurant $model
     */
    public function retrieved(Restaurant $model)
    {
    }
}
