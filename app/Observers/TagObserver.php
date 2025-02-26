<?php

namespace App\Observers;

use App\Events\ChangeLogs;
use App\Models\Tag;

class TagObserver
{
    /**
     * @param Tag $model
     */
    public function creating(Tag $model)
    {
    }

    /**
     * @param Tag $model
     */
    public function created(Tag $model)
    {
        //event(new ChangeLogs($model, 'created'));
    }

    /**
     * @param Tag $model
     */
    public function updating(Tag $model)
    {
    }

    /**
     * @param Tag $model
     */
    public function updated(Tag $model)
    {
        //event(new ChangeLogs($model, 'updated'));
    }

    /**
     * @param Tag $model
     */
    public function saving(Tag $model)
    {
    }

    /**
     * @param Tag $model
     */
    public function saved(Tag $model)
    {
    }

    /**
     * @param Tag $model
     */
    public function deleting(Tag $model)
    {
    }

    /**
     * @param Tag $model
     */
    public function deleted(Tag $model)
    {
        //event(new ChangeLogs($model, 'deleted'));
    }

    /**
     * @param Tag $model
     */
    public function restoring(Tag $model)
    {
    }

    /**
     * @param Tag $model
     */
    public function restored(Tag $model)
    {
    }

    /**
     * @param Tag $model
     */
    public function retrieved(Tag $model)
    {
    }
}
