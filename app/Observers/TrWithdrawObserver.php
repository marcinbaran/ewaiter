<?php

namespace App\Observers;

use App\Events\ChangeLogs;
use App\TrWithdrawal;

class TrWithdrawObserver
{
    /**
     * @param TrWithdrawal $model
     */
    public function creating(TrWithdrawal $model)
    {
    }

    /**
     * @param TrWithdrawal $model
     */
    public function created(TrWithdrawal $model)
    {
        event(new ChangeLogs($model, 'created'));
    }

    /**
     * @param TrWithdrawal $model
     */
    public function updating(TrWithdrawal $model)
    {
    }

    /**
     * @param TrWithdrawal $model
     */
    public function updated(TrWithdrawal $model)
    {
        event(new ChangeLogs($model, 'updated'));
    }

    /**
     * @param TrWithdrawal $model
     */
    public function saving(TrWithdrawal $model)
    {
    }

    /**
     * @param TrWithdrawal $model
     */
    public function saved(TrWithdrawal $model)
    {
    }

    /**
     * @param TrWithdrawal $model
     */
    public function deleting(TrWithdrawal $model)
    {
    }

    /**
     * @param TrWithdrawal $model
     */
    public function deleted(TrWithdrawal $model)
    {
        event(new ChangeLogs($model, 'deleted'));
    }

    /**
     * @param TrWithdrawal $model
     */
    public function restoring(TrWithdrawal $model)
    {
    }

    /**
     * @param TrWithdrawal $model
     */
    public function restored(TrWithdrawal $model)
    {
    }

    /**
     * @param TrWithdrawal $model
     */
    public function retrieved(TrWithdrawal $model)
    {
    }
}
