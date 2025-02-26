<?php

namespace App\Events;

use App\Managers\NotificationManager;
use App\Order;
use App\Services\TranslationService;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class OrderStatusEvent
{
    use Dispatchable,
        InteractsWithSockets,
        SerializesModels;

    /**
     * @var Order
     */
    protected $order;

    /**
     * @var NotificationManager
     */
    protected $manager;

    /**
     * @var bool
     */
    private static $isStatusChanged = false;

    /**
     * Create a new event instance.
     */
    public function __construct(Order $order, TranslationService $translationService = null)
    {
        $this->order = $order;
        $this->manager = new NotificationManager($translationService);
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('channel-name');
    }

    /**
     * @return OrderStatusEvent
     */
    public function notificationSent(): self
    {
        if (self::$isStatusChanged) {
            $request = request();
            $request->request->add(['type' => 'status_order', 'order' => ['id' => $this->order->id], 'url' => Route('admin.orders.show', ['order' => $this->order->id])]);
            switch($this->order->status) {
                case Order::STATUS_NEW:
                    $request->request->add(['description' => __('orders.Order has been placed')]);
                    break;
                case Order::STATUS_ACCEPTED:
                    $request->request->add(['description' => __('orders.Order has been accepted')]);
                    break;
                case Order::STATUS_READY:
                    $request->request->add(['description' => __('orders.Order is ready')]);
                    break;
                case Order::STATUS_RELEASED:
                    $request->request->add(['description' => __('orders.Order has been released')]);
                    break;
                case Order::STATUS_CANCELED:
                    $request->request->add(['description' => __('orders.Order has been canceled')]);
                    break;
                default:
                    $request->request->add(['description' => __('orders.Order has unknown status')]);
                    break;
            }
            $this->manager->create($request);
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
