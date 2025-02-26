<?php

namespace App\Events;

use App\Enum\DeliveryMethod;
use App\Managers\NotificationManager;
use App\Models\Bill;
use App\Services\FirebaseServiceV2;
use Carbon\Carbon;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class BillStatusMobileEvent
{
    use Dispatchable,
        InteractsWithSockets,
        SerializesModels;

    protected Bill $bill;

    protected DeliveryMethod $deliveryMethod;

    /**
     * @var NotificationManager
     */
    protected $manager;

    /**
     * @var bool
     */
    private static $isMobileStatusChanged = false;

    /**
     * Create a new event instance.
     */
    public function __construct(Bill $bill)
    {
        $this->bill = $bill;
        $this->deliveryMethod = DeliveryMethod::from($bill->delivery_type);
        $this->manager = new NotificationManager();
    }

    /**
     * @return BillStatusMobileEvent
     */
    public function notificationMobileSent(): self
    {
        $request = request();
        $request->request->add(
            [
                'type' => 'status_bill_mobile',
                'bill' => ['id' => $this->bill->id],
                'url' => Route('admin.bills.show',
                    ['bill' => $this->bill->id])
            ]
        );

        $description = $this->getNotificationDescription();
        $request->request->add(['description' => $description]);
        $this->manager->create($request);

        if ($this->isPushable()) {
            FirebaseServiceV2::saveNotification($this->bill->user_id, $description, '/account/orders_history/' . $this->bill->id, $this->bill->id);
        }

        return $this;
    }

    /**
     * @param bool $isMobileStatusChanged
     */
    public static function setMobileStatusChanged(bool $isMobileStatusChanged = false)
    {
        self::$isMobileStatusChanged = $isMobileStatusChanged;
    }

    private function isPushable(): bool
    {
        if (in_array($this->bill->status, [Bill::STATUS_CANCELED])) {
            return true;
        }

        if ($this->deliveryMethod === DeliveryMethod::PERSONAL_PICKUP && $this->bill->status === Bill::STATUS_READY) {
            return true;
        }

        if ($this->deliveryMethod === DeliveryMethod::DELIVERY_TO_ADDRESS && $this->bill->status === Bill::STATUS_READY) {
            return true;
        }

        if ($this->bill->status === Bill::STATUS_RELEASED) {
            return true;
        }

        if ($this->deliveryMethod == DeliveryMethod::TABLE_DELIVERY && $this->bill->status === Bill::STATUS_READY) {
            return true;
        }

        return false;
    }

    private function getNotificationDescription()
    {
        switch ($this->bill->status) {
            case Bill::STATUS_ACCEPTED:
                if ($this->bill->time_wait !== null) {
                    $deliveryTime = Carbon::parse($this->bill->time_wait)->format('Y-m-d H:i');

                    if ($this->deliveryMethod === DeliveryMethod::PERSONAL_PICKUP) {
                        return __('firebase.Your order has been accepted [with time, personal pickup]', ['delivery_time' => $deliveryTime]);
                    }

                    if ($this->deliveryMethod === DeliveryMethod::TABLE_DELIVERY) {
                        return __('firebase.Your order has been accepted [with time, table delivery]', ['delivery_time' => $deliveryTime]);
                    }

                    return __('firebase.Your order has been accepted [with time]', ['delivery_time' => $deliveryTime]);
                }

                return __('firebase.Your order has been accepted');

            case Bill::STATUS_READY:
                if ($this->deliveryMethod === DeliveryMethod::PERSONAL_PICKUP) {
                    return __('firebase.Your order is ready pickup');
                }

                if ($this->deliveryMethod == DeliveryMethod::ROOM_DELIVERY) {
                    return __('firebase.Your order has been released to room');
                } elseif ($this->deliveryMethod == DeliveryMethod::TABLE_DELIVERY) {
                    return __('firebase.Your order has been released to table');
                }

                return __('firebase.Your order is ready');
            case Bill::STATUS_RELEASED:
                if ($this->deliveryMethod == DeliveryMethod::ROOM_DELIVERY) {
                    return __('firebase.Your order has been released to room');
                } elseif ($this->deliveryMethod == DeliveryMethod::TABLE_DELIVERY) {
                    return __('firebase.Your order has been released');
                }

                return __('firebase.Your order has been released');
            case Bill::STATUS_CANCELED:
                return __('firebase.Your order has been canceled');
            default:
                return __('firebase.Order has unknown status');
        }
    }
}
