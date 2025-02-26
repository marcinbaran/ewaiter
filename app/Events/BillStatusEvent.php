<?php

namespace App\Events;

use App\Managers\NotificationManager;
use App\Models\Bill;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class BillStatusEvent
{
    use Dispatchable,
        InteractsWithSockets,
        SerializesModels;

    /**
     * @var bool
     */
    private static $isStatusChanged = false;

    /**
     * Create a new event instance.
     */
    public function __construct(
        public Bill $bill
    ) {
    }

    public function notificationSent(): self
    {
        if (self::$isStatusChanged || $this->bill->status == Bill::STATUS_NEW) {
            $request = request();
            $request->request->add([
                'type' => 'status_bill',
                'bill' => [
                    'id' => $this->bill->id,
                ],
                'url' => Route('admin.bills.show', ['bill' => $this->bill->id]),
            ]);
            switch($this->bill->status) {
                case Bill::STATUS_NEW:
                    $request->request->add(['description' => __('orders.Order has been placed')]);
                    break;
                case Bill::STATUS_ACCEPTED:
                    $request->request->add(['description' => __('orders.Order has been accepted')]);
                    break;
                case Bill::STATUS_READY:
                    $request->request->add(['description' => __('orders.Order is ready')]);
                    break;
                case Bill::STATUS_RELEASED:
                    $request->request->add(['description' => __('orders.Order has been released')]);
                    break;
                case Bill::STATUS_CANCELED:
                    $request->request->add(['description' => __('orders.Order has been canceled')]);
                    break;
                default:
                    $request->request->add(['description' => __('orders.Order has unknown status')]);
                    break;
            }
            (new NotificationManager())->create($request);
            self::$isStatusChanged = false;
        }

        return $this;
    }

    /**
     * @param bool $isStatusChanged
     */
    public static function setStatusChanged(bool $isStatusChanged = false)
    {
        self::$isStatusChanged = $isStatusChanged;
    }
}
