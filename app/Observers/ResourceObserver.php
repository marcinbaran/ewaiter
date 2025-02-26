<?php

namespace App\Observers;

use App\Events\ChangeLogs;
use App\Models\Resource;
use App\Services\UploadService;

class ResourceObserver
{
    /**
     * @param resource $model
     */
    public function created(Resource $model)
    {
        event(new ChangeLogs($model, 'created'));
    }

    /**
     * @param resource $model
     */
    public function updated(Resource $model)
    {
        event(new ChangeLogs($model, 'updated'));
    }

    /**
     * @param resource $model
     */
    public function saving(Resource $model)
    {
    }

    /**
     * @param resource $model
     */
    public function saved(Resource $model)
    {
    }

    /**
     * @param resource $model
     */
    public function deleting(Resource $model)
    {
    }

    public function deleted(Resource $model)
    {
        (new UploadService())->removeResource($model);
    }

    /**
     * @param resource $model
     */
    public function restoring(Resource $model)
    {
    }

    /**
     * @param resource $model
     */
    public function restored(Resource $model)
    {
    }

    /**
     * @param resource $model
     */
    public function retrieved(Resource $model)
    {
    }
}
