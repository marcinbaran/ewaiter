<?php

namespace App\Handlers\Query\Review;

use App\Models\Review;
use App\Queries\Review\GetReviewsQuery;
use Ecotone\Modelling\Attribute\QueryHandler;
use Ecotone\Modelling\QueryBus;

class GetReviewHandler
{
    public function __construct(
        protected QueryBus $commandBus,
    ) {
    }

    #[QueryHandler]
    public function getReviews(GetReviewsQuery $query)
    {
        if ($query->isSearchById()) {
            return Review::findOrFail($query->getReviewId());
        }

        $reviewQuery = Review::query()->orderBy('id', 'desc');

        $reviewQuery->when($query->isSearchByRestaurantId(), function ($q) use ($query) {
            return $q->where('restaurant_id', $query->getRestaurantId());
        });

        $reviewQuery->when($query->isSearchByBillId(), function ($q) use ($query) {
            return $q->where('bill_id', $query->getBillId());
        });

        return $reviewQuery->get();
    }
}
