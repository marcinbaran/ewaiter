<?php

namespace App\Events;

use App\Models\Bill;
use App\Models\Order;
use App\Models\Promotion;
use App\Models\Settings;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PromotionBillEvent implements PromotionInterface
{
    use Dispatchable,
        InteractsWithSockets,
        SerializesModels;

    /**
     * @var Bill
     */
    protected $bill;

    /**
     * Create a new event instance.
     */
    public function __construct(Bill $bill)
    {
        $this->bill = $bill;
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
     * @return PromotionBillEvent
     */
    public function calculate(): self
    {
        $this->bill->discount = 0;
        $orders = $this->bill->orders->filter(function ($order) {
            return Order::STATUS_CANCELED != $order->status;
        });
        foreach ($orders as $order) {
            event(new PromotionOrderEvent($order));
            $order = $order->fresh();
            $this->bill->discount += $order->discount;
        }
        $promotions = Promotion::findActiveOnBill($this->bill);
        Promotion::findActiveForBillAndOrderDish($this->bill)->count() < 2 ?: $promotions = $promotions->filter(function ($promotion) {
            return $promotion->merge = Promotion::MERGE_YES;
        });
        $discount = $this->bill->discount;
        foreach ($promotions as $promotion) {
            if ($promotion->type == Promotion::TYPE_ON_BUNDLE) {
                $this->bill->discount += Promotion::TYPE_VALUE_PRICE == $promotion->type_value ? Promotion::onBundleCalculatePrice($this->bill, $promotion) : Promotion::onBundleCalculatePercentage($this->bill, $promotion);
            } else {
                $this->bill->discount += Promotion::TYPE_VALUE_PRICE == $promotion->type_value ? $promotion->value : (($this->bill->price - $discount) * $promotion->value) / 100;
            }
            if ($this->bill->discount >= $this->bill->price) {
                $this->bill->discount = $this->bill->price;

                break;
            }
        }

        if ($this->bill->room_delivery) {
            $service_charge = Settings::getSetting('service_charge', 'service_charge', true, false);
            $initial_price = ($this->bill->price - $this->bill->discount);
            if ($service_charge) {
                $service_charge = (float) $service_charge;
                $this->bill->price = ($this->bill->price - $this->bill->discount) + $service_charge;
                $this->bill->service_charge = $service_charge;
            }
            $service_charge_percent = Settings::getSetting('service_charge', 'service_charge_procent', true, false);
            if ($service_charge_percent) {
                $service_charge_percent = (float) trim(str_replace('%', '', $service_charge_percent));
                $service_charge_percent_value = $initial_price * ($service_charge_percent / 100);
                $this->bill->price = ($this->bill->price - $this->bill->discount) + $service_charge_percent_value;
                $this->bill->service_charge = $this->bill->service_charge + $service_charge_percent_value;
            }
        }

        $this->bill->update();

        return $this;
    }
}
