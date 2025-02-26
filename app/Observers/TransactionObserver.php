<?php

namespace App\Observers;

use App\Events\ChangeLogs;
use App\Transaction;

class TransactionObserver
{
    /**
     * @param Transaction $model
     */
    public function creating(Transaction $model)
    {
    }

    /**
     * @param Transaction $model
     */
    public function created(Transaction $model)
    {
        event(new ChangeLogs($model, 'created'));
    }

    /**
     * @param Transaction $model
     */
    public function updating(Transaction $model)
    {
    }

    /**
     * @param Transaction $model
     */
    public function updated(Transaction $model)
    {
        event(new ChangeLogs($model, 'updated'));
    }

    /**
     * @param Transaction $model
     */
    public function saving(Transaction $model)
    {
    }

    /**
     * @param Transaction $model
     */
    public function saved(Transaction $model)
    {
    }

    /**
     * @param Transaction $model
     */
    public function deleting(Transaction $model)
    {
    }

    /**
     * @param Transaction $model
     */
    public function deleted(Transaction $model)
    {
        event(new ChangeLogs($model, 'deleted'));
    }

    /**
     * @param Transaction $model
     */
    public function restoring(Transaction $model)
    {
    }

    /**
     * @param Transaction $model
     */
    public function restored(Transaction $model)
    {
    }

    /**
     * @param Transaction $model
     */
    public function retrieved(Transaction $model)
    {
    }
}
