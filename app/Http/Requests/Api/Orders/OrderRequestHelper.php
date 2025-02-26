<?php

namespace App\Http\Requests\Api\Orders;

use App\Http\Requests\Api\PlaceOrderRequest;
use App\Models\Dish;

final class OrderRequestHelper
{
    public static function getOrderTotalPriceAfterDiscount($request): float
    {
        return self::getOrderTotalPrice($request) - self::getOrderTotalDiscount($request);
    }

    public static function getOrderTotalPrice($request): float
    {
        $totalDishesPrice = 0;
        $totalAdditionsPrice = 0;

        foreach ($request->get(PlaceOrderRequest::ORDERS_PARAM_KEY) as $order) {
            if (isset($order['bundle'])) {
                $totalDishesPrice += $order['bundle']['price'];
            } else {
                $totalDishesPrice += $order[PlaceOrderRequest::DISH_PARAM_KEY][PlaceOrderRequest::DISH_PRICE_PARAM_KEY] * $order[PlaceOrderRequest::QUANTITY_PARAM_KEY];
            }

            $totalAdditionsPrice += self::getOrderAdditionsTotalPrice($order);
        }

        return $totalDishesPrice + $totalAdditionsPrice;
    }

    public static function getOrderAdditionsTotalPrice(array $order): float
    {
        $additionsPrice = 0;

        foreach ($order[PlaceOrderRequest::ADDITIONS_PARAM_KEY] as $addition) {
            $additionsPrice += $addition[PlaceOrderRequest::ADDITION_PRICE_PARAM_KEY] * $order[PlaceOrderRequest::QUANTITY_PARAM_KEY];
        }

        return $additionsPrice;
    }

    public static function getOrderTotalDiscount($request): float
    {
        $totalDiscount = 0;

        foreach ($request->get(PlaceOrderRequest::ORDERS_PARAM_KEY) as $order) {
            if (! empty($order[PlaceOrderRequest::PROMOTION_DISCOUNT_PARAM_KEY])) {
                $totalDiscount += $order[PlaceOrderRequest::PROMOTION_DISCOUNT_PARAM_KEY][PlaceOrderRequest::PROMOTION_DISCOUNT_AMOUNT_PARAM_KEY];
            }
        }

        return $totalDiscount;
    }

    public static function getMandatoryAdditionGroupsForDish($dishId): array
    {
        $mandatoryAdditionGroups = [];

        foreach (Dish::find($dishId)->getAdditionGroups() as $basePivot) {
            $additionGroup = $basePivot->addition_group;

            if (! $additionGroup->mandatory) {
                continue;
            }

            foreach ($additionGroup->additions_additions_groups as $additionGroupAdditionPivot) {
                $mandatoryAdditionGroups[$dishId][$additionGroupAdditionPivot->addition_group_id][] = $additionGroupAdditionPivot->addition_id;
            }
        }

        return $mandatoryAdditionGroups;
    }

    public static function getSelectedAdditionsIds($order): array
    {
        $selectedAdditionIds = [];

        foreach ($order[PlaceOrderRequest::ADDITIONS_PARAM_KEY] as $selectedAddition) {
            if ($selectedAddition[PlaceOrderRequest::ADDITION_QUANTITY_PARAM_KEY] > 0) {
                $selectedAdditionIds[] = $selectedAddition[PlaceOrderRequest::ADDITION_ID_PARAM_KEY];
            }
        }

        return $selectedAdditionIds;
    }

    public static function getDishIdFromOrder($order): int
    {
        return $order[PlaceOrderRequest::DISH_PARAM_KEY][PlaceOrderRequest::ADDITION_GROUP_ID_PARAM_KEY];
    }
}
