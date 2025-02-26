<?php

namespace App\Services;

use App\Models\Bill;
use App\Models\DeliveryRange;
use App\Models\Restaurant;

class RestaurantUpdateService
{
    private Restaurant $restaurant;

    private DeliveryRange $deliveryRange;

    public function __construct(Restaurant $restaurant, DeliveryRange $deliveryRange)
    {
        $this->restaurant = $restaurant;
        $this->deliveryRange = $deliveryRange;
    }

    public function updateRestaurant()
    {
        $this->restaurant->order_minimal_price = $this->deliveryRange->min_value;
        $this->restaurant->distance = $this->deliveryRange->range_to;
        $this->restaurant->average_price = $this->getAveragePrice();

        $this->restaurant->orders_count = Bill::query()->count();

        $this->restaurant->save();
    }

    public function getAveragePrice()
    {
        return $this->restaurant->getAveragePrice();
    }
}
