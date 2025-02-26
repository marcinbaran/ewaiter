<?php

namespace App\Queries\Review;

use App\Http\Requests\Api\ReviewRequest;
use App\Models\Review;
use App\Queries\QueryInterface;

class GetReviewsQuery implements QueryInterface
{
    public function __construct(
        private ?int $id,
        private ?int $restaurant_id,
        private ?int $bill_id,
    ) {
    }

    public static function createFromRequest(ReviewRequest $request): self
    {
        return new self(
            id: $request->get(ReviewRequest::ID_KEY),
            restaurant_id: $request->get(ReviewRequest::RESTAURANT_ID_KEY),
            bill_id: $request->get(ReviewRequest::BILL_ID_KEY),
        );
    }

    public function getReviewId(): int
    {
        return $this->id;
    }

    public function getRestaurantId(): int
    {
        return $this->restaurant_id;
    }

    public function getBillId(): int
    {
        return $this->bill_id;
    }

    public function isSearchById(): bool
    {
        return $this->id !== null;
    }

    public function isSearchByRestaurantId(): bool
    {
        return $this->restaurant_id !== null;
    }

    public function isSearchByBillId(): bool
    {
        return $this->bill_id !== null;
    }

    public function execute()
    {
        $query = Review::query()->whereNull('deleted_at')->orderBy('created_at', 'desc');

        if ($this->isSearchById()) {
            $query->where('id', $this->getReviewId());
        }

        if ($this->isSearchByRestaurantId()) {
            $query->where('restaurant_id', $this->getRestaurantId());
        }

        if ($this->isSearchByBillId()) {
            $query->where('bill_id', $this->getBillId());
        }

        return $query->get();
    }
}
