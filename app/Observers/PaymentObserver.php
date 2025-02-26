<?php

namespace App\Observers;

use App\Events\ChangeLogs;
use App\Models\Payment;

class PaymentObserver
{
    /**
     * @param Payment $model
     */
    public function creating(Payment $model)
    {
    }

    /**
     * @param Payment $model
     */
    public function created(Payment $model)
    {
        event(new ChangeLogs($model, 'created'));
    }

    /**
     * @param Payment $model
     */
    public function updating(Payment $model)
    {
    }

    /**
     * @param Payment $model
     */
    public function updated(Payment $model)
    {
        event(new ChangeLogs($model, 'updated'));
    }

    /**
     * @param Payment $model
     */
    public function saving(Payment $model)
    {
    }

    /**
     * @param Payment $model
     */
    public function saved(Payment $model)
    {
        if ($model->isDirty('paid')) {
            $model->bill()->update(['paid' => $model->paid]);
        }
    }

    /**
     * @param Payment $model
     */
    public function deleting(Payment $model)
    {
    }

    /**
     * @param Payment $model
     */
    public function deleted(Payment $model)
    {
        event(new ChangeLogs($model, 'deleted'));
    }

    /**
     * @param Payment $model
     */
    public function restoring(Payment $model)
    {
    }

    /**
     * @param Payment $model
     */
    public function restored(Payment $model)
    {
    }

    /**
     * @param Payment $model
     */
    public function retrieved(Payment $model)
    {
    }
}
