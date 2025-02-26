<?php

namespace App\Observers;

use App\Events\ChangeLogs;
use App\Models\Dish;
use OwenIt\Auditing\AuditableObserver;
use OwenIt\Auditing\Contracts\Auditable;

class DishObserver extends AuditableObserver
{

    /**
     * @param Dish $model
     */
    public function deleted(Auditable $model)
    {
        $model->photos()->delete();

        parent::deleted($model);
    }
}
