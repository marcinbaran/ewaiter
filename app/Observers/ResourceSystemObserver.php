<?php

namespace App\Observers;

use App\Events\ChangeLogs;
use App\Models\ResourceSystem;

class ResourceSystemObserver
{
    use UploadTrait;

    /**
     * @param resource $model
     */
    public function creating(ResourceSystem $model)
    {
    }

    /**
     * @param resource $model
     */
    public function created(ResourceSystem $model)
    {
        // event(new ChangeLogs($model, 'created'));
    }

    /**
     * @param resource $model
     */
    public function updating(ResourceSystem $model)
    {
    }

    /**
     * @param resource $model
     */
    public function updated(ResourceSystem $model)
    {
        event(new ChangeLogs($model, 'updated'));
    }

    /**
     * @param resource $model
     */
    public function saving(ResourceSystem $model)
    {
    }

    /**
     * @param resource $model
     */
    public function saved(ResourceSystem $model)
    {
    }

    /**
     * @param resource $model
     */
    public function deleting(ResourceSystem $model)
    {
    }

    /**
     * @param resource $model
     */
    public function deleted(ResourceSystem $model)
    {
    }

    /**
     * @param resource $model
     */
    public function restoring(ResourceSystem $model)
    {
    }

    /**
     * @param resource $model
     */
    public function restored(ResourceSystem $model)
    {
    }

    /**
     * @param resource $model
     */
    public function retrieved(ResourceSystem $model)
    {
    }
}
