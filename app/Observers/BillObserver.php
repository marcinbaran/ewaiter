<?php

namespace App\Observers;

use App\Enum\Commission\CommissionStatus;
use App\Enum\OrderStatus;
use App\Events\BillStatusEvent;
use App\Events\BillStatusMobileEvent;
use App\Events\ChangeLogs;
use App\Events\Orders\OrderEvent;
use App\Helpers\CommissionHelper;
use App\Models\Bill;
use App\Models\Commission;
use App\Models\FirebaseNotification;
use App\Models\Refund;
use App\Models\Restaurant;
use Carbon\Carbon;
use OwenIt\Auditing\AuditableObserver;
use OwenIt\Auditing\Contracts\Auditable;

class BillObserver extends AuditableObserver
{
    /**
     * @param Bill $model
     */
    public function creating(Bill $model)
    {
    }

    /**
     * @param Bill $model
     */
    public function created(Auditable $model)
    {
        event(new ChangeLogs($model, 'created'));
        BillStatusEvent::setStatusChanged(true);
        event(new BillStatusEvent($model));

        $model->user_res_id = auth()->user()->res_id;
        $model->save();
        event(new OrderEvent($model, OrderStatus::NEW->name));
        parent::created($model);
    }

    /**
     * @param Bill $model
     */
    public function updated(Auditable $model)
    {
        if (! $model->is_commission_charged && $model->isDirty('status') && $model->status === Bill::STATUS_ACCEPTED) {
//            event(new OrderEvent($model, OrderStatus::ACCEPTED->name));
            $this->saveCommission($model);
        }
        if ($model->isDirty('status') && $model->status === Bill::STATUS_RELEASED && $model->paid == 0) {
//            event(new OrderEvent($model, OrderStatus::RELEASED->name));
            $this->updateOrderPaymentStatusToPaid($model);
        }

        event(new ChangeLogs($model, 'updated'));

        parent::updated($model);
    }

    private function saveCommission(Bill $bill): void
    {
        $currentRestaurant = Restaurant::getCurrentRestaurant();

        Commission::create([
            'restaurant_id' => $currentRestaurant->id,
            'restaurant_name' => $currentRestaurant->name,
            'bill_id' => $bill->id,
            'bill_price' => $bill->getFullPrice(),
            'commission' => CommissionHelper::calculateCommission($bill->getDiscountedPrice(), $currentRestaurant->provision),
            'status' => CommissionStatus::ACTIVE,
        ]);

        $bill->is_commission_charged = true;
        $bill->saveQuietly();
    }

    private function updateOrderPaymentStatusToPaid(Bill $model)
    {
        event(new OrderEvent($model, "PAID"));
        $model->update(['paid' => 1]);
    }

    /**
     * @param Bill $model
     */
    public function saving(Bill $model)
    {
//        if ($model->isDirty('time_wait')) {
//            $model->time_wait = $model->time_wait ?? Bill::TIME_WAIT;
//            $model->time_wait = is_numeric($model->time_wait) ? Carbon::now()->modify('+'.$model->time_wait.' minutes') : Carbon::createFromFormat($model->time_wait);
//        }
        if ($model->isDirty('paid') && $model->paid && null == $model->payment_at) {
            $model->payment_at = Carbon::now();
        }
    }

    private function updateOrdersStatus(Bill $model)
    {
        event (new OrderEvent($model,$model->status));

        $model->orders()->update(['status' => $model->status]);
    }

    private function sendPushNotifications(Bill $model)
    {
        if ($model->isDirty('status')) {
            FirebaseNotification::where('read_at', null)
                ->where('object_id', $model->id)
                ->where('object', 'bills')
                ->update(['read_at' => date('Y-m-d H:i:s')]);

            BillStatusEvent::setStatusChanged(in_array($model->status, [Bill::STATUS_ACCEPTED, Bill::STATUS_RELEASED]));
            BillStatusMobileEvent::setMobileStatusChanged(true);

            event(new BillStatusMobileEvent($model));
            event(new BillStatusEvent($model));
        }
    }

    private function refundIfCanceled(Bill $model)
    {
        if (
            $model->status == Bill::STATUS_CANCELED &&
            $model->isRefund() &&
            ! Refund::where('bill_id', '=', $model->id)
                ->whereBetween('status', [Refund::STATUS_TO_REFUNDED, Refund::STATUS_REFUNDED])
                ->exists()
        ) {
            Refund::create([
                'payment_id' => $model->payment->id,
                'bill_id' => $model->id,
                'amount' => $model->payment->p24_amount / 100,
                'status' => Refund::STATUS_TO_REFUNDED,
            ]);
        }
    }

    /**
     * @param Bill $model
     */
    public function saved(Bill $model)
    {
        if ($model->wasChanged('status')) {
            $this->updateOrdersStatus($model);
            $this->sendPushNotifications($model);
            $model->checkPhoneNotifications();
            $this->refundIfCanceled($model);
        }

        if ($model->isDirty('paid')) {
            $model->markPaidOrders();
        }

        if ($model->isDirty('paid') && ! $model->ticket && $model->paid && $model->paid_type == 'card_tpay') {
            $model->ticket = 1;
            $model->save();
        }

        if ($model->status > 0 && ! $model->time_wait) {
            if (count($model->orders)) {
                $time_wait = 0;
                foreach ($model->orders as $order) {
                    if (isset($order->dish->time_wait) && $order->dish->time_wait > $time_wait) {
                        $time_wait = $order->dish->time_wait;
                    }
                }
                $model->time_wait = $time_wait ?? Bill::TIME_WAIT;
                $model->time_wait = is_numeric($model->time_wait) ? Carbon::now()->modify('+'.$model->time_wait.' minutes') : Carbon::createFromFormat($model->time_wait);
                $model->saveQuietly();
            }
        }

        if ($model->status == Bill::STATUS_CANCELED && $model->points /* && $model->paid */ && ! $model->points_refunded) {
            if ($model->refundPoints()) {
                $model->points_refunded = true;
                $model->save();
            }
        }

        if ($model->isDirty('paid') && $model->paid && $model->points == 0) {
            $model->grantCashback();
        }
    }

    /**
     * @param Bill $model
     */
    public function deleted(Auditable $model)
    {
        event(new ChangeLogs($model, 'deleted'));

        parent::deleted($model);
    }

}
