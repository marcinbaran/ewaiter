<?php

namespace App\Events;

use App\Order;
use App\Promotion;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PromotionOrderEvent implements PromotionInterface
{
    use Dispatchable,
        InteractsWithSockets,
        SerializesModels;

    /**
     * @var Order
     */
    protected $order;

    /**
     * Create a new event instance.
     */
    public function __construct(Order $order)
    {
        $this->order = $order;
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
     * @return PromotionOrderEvent
     */
    public function calculate(): self
    {
        $promotions = Promotion::findActiveOnOrderDish($this->order);
        if (! $promotions->count()) {
            return $this;
        }
        $this->order->discount = 0;

        $order = $this->order;
        $orders = [];
        //only return promotions for dishes that are in the order
        $promotions = $promotions->filter(function ($promotion) use ($order, &$orders) {
            return null === $promotion->gift_dish_id || $promotion->gift_dish_id == $order->dish_id || ($orders[$promotion->gift_dish_id] = Order::findGiftDishOnBill($promotion->gift_dish_id, $order->bill_id));
        });
        if (! $promotions->count()) {
            return $this;
        }
        array_walk($orders, function (&$ordersGift) {
            foreach ($ordersGift as $orderGift) {
                $orderGift->discount = 0;
            }
        });
        //check if the bill has other promotions and return only those that can be combined
        Promotion::findActiveForBillAndOrderDish($this->order->bill)->count() < 2 ?: $promotions = $promotions->filter(function ($promotion) {
            return $promotion->merge = Promotion::MERGE_YES;
        });
        if (! $promotions->count()) {
            return $this;
        }

        $sumQuantity = (int) Order::sumQuantityOrderDishOnBill($this->order->dish_id, $this->order->bill_id);
        $sumQuantityWithDiscount = (int) Order::sumQuantityOrderDishOnBill($this->order->dish_id, $this->order->bill_id, true);
        foreach ($promotions as $promotion) {
            $limit = floor($sumQuantity - $sumQuantityWithDiscount / $promotion->min_quantity_order_dish);

            if (! $limit || (null !== $promotion->max_quantity_gift_dish && $promotion->max_quantity_gift_dish <= $sumQuantityWithDiscount)) {
                continue;
            }
            if (null === $promotion->gift_dish_id || $promotion->gift_dish_id == $this->order->dish_id) {
                $this->upDiscount($this->order, $promotion, $limit);
            } elseif (! empty($orders[$promotion->gift_dish_id])) {
                foreach ($orders[$promotion->gift_dish_id] as $orderGift) {
                    $this->upDiscount($orderGift, $promotion, $limit);
                }
            }
        }

        return $this;
    }

    /**
     * @param Order     $order
     * @param Promotion $promotion
     *
     * @return PromotionOrderEvent
     */
    private function upDiscount(Order $order, Promotion $promotion, int $limit): self
    {
        $maxQuantity = (null !== $promotion->max_quantity_gift_dish && $limit > $promotion->max_quantity_gift_dish ? $promotion->max_quantity_gift_dish : $limit);
        $maxQuantity < $order->quantity ?: $maxQuantity = $order->quantity;
        if ($order->discount == ($order->price * $maxQuantity)) {
            return $this;
        }
        $order->discount += (Promotion::TYPE_VALUE_PRICE == $promotion->type_value ? $promotion->value : ($order->price * $promotion->value) / 100) * $maxQuantity;
        if ($order->discount > ($order->price * $maxQuantity)) {
            $order->discount = ($order->price * $maxQuantity);
        }
        $order->update();

        return $this;
    }
}
