<?php

namespace App\Observers;

use App\Events\ChangeLogs;
use App\Models\Promotion;

class PromotionObserver
{
    /**
     * @param Promotion $model
     */
    public function creating(Promotion $model)
    {
    }

    /**
     * @param Promotion $model
     */
    public function created(Promotion $model)
    {
        event(new ChangeLogs($model, 'created'));
    }

    /**
     * @param Promotion $model
     */
    public function updating(Promotion $model)
    {
    }

    /**
     * @param Promotion $model
     */
    public function updated(Promotion $model)
    {
        event(new ChangeLogs($model, 'updated'));
    }

    /**
     * @param Promotion $model
     */
    public function saving(Promotion $model)
    {
        switch ($model->type) {
            case Promotion::TYPE_ON_DISH:
                $model->order_category_id = null;
                $model->min_price_bill = 0;
                break;
            case Promotion::TYPE_ON_BILL:
                $model->order_category_id = null;
                $model->order_dish_id = null;
                $model->gift_dish_id = null;
                $model->min_quantity_order_dish = 1;
                $model->max_quantity_gift_dish = null;

                break;
            case Promotion::TYPE_ON_CATEGORY:
                $model->min_price_bill = 0;
                $model->order_dish_id = null;
                $model->gift_dish_id = null;
                $model->min_quantity_order_dish = 1;
                $model->max_quantity_gift_dish = null;
                break;
            case Promotion::TYPE_ON_BUNDLE:
                $model->order_category_id = null;
                $model->order_dish_id = null;
                $model->gift_dish_id = null;
                $model->min_price_bill = 0;
                break;
        }
    }

    /**
     * @param Promotion $model
     */
    public function saved(Promotion $model)
    {
    }

    /**
     * @param Promotion $model
     */
    public function deleting(Promotion $model)
    {
    }

    /**
     * @param Promotion $model
     */
    public function deleted(Promotion $model)
    {
        $model->photo()->delete();
        $model->promotion_dishes()->delete();
        event(new ChangeLogs($model, 'updated'));
    }

    /**
     * @param Promotion $model
     */
    public function restoring(Promotion $model)
    {
    }

    /**
     * @param Promotion $model
     */
    public function restored(Promotion $model)
    {
    }

    /**
     * @param Promotion $model
     */
    public function retrieved(Promotion $model)
    {
    }
}
