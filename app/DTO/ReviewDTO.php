<?php

namespace App\DTO;

use Illuminate\Database\Eloquent\Collection;

class ReviewDTO
{
    const string RESTAURANT_ID_KEY = 'restaurant_id';
    const string BILL_ID_KEY = 'bill_id';
    const string OBJECT_TYPE_KEY = 'object_type';
    const string FOOD_KEY = 'food';
    const string DELIVERY_KEY = 'delivery';
    const string RATING_KEY = 'rating';
    const string COMMENT_KEY = 'comment';

    public function __construct(private readonly Collection $reviews)
    {
    }

    public function transformData(): array
    {
        $mergedData = [];

        foreach ($this->reviews as $review) {
            $billId = $review[self::BILL_ID_KEY];
            $restaurantId = $review[self::RESTAURANT_ID_KEY];
            $type = $review[self::OBJECT_TYPE_KEY];

            if (!isset($mergedData[$billId])) {
                $mergedData[$billId] = [
                    self::RESTAURANT_ID_KEY => $restaurantId,
                    self::BILL_ID_KEY => $billId,
                    self::FOOD_KEY => null,
                    self::DELIVERY_KEY => null
                ];
            }
            $mergedData[$billId][$type] = [
                self::RATING_KEY => $review[self::RATING_KEY],
                self::COMMENT_KEY => $review[self::COMMENT_KEY]
            ];
        }

        return array_values($mergedData);
    }
}
