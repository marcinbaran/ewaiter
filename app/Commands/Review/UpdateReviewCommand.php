<?php

namespace App\Commands\Review;

use App\Commands\CommandInterface;
use App\Http\Requests\Api\ReviewRequest;

readonly class UpdateReviewCommand implements CommandInterface
{
    public function __construct(
        private int $id,
        private int $rating_food,
    )
    {
    }

    public static function createFromRequest(ReviewRequest $request): self
    {
        return new self(
            id: $request->get(ReviewRequest::ID_KEY),
            rating_food: $request->get(ReviewRequest::RATING_FOOD_KEY),
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'rating_food' => $this->rating_food,
        ];
    }

    public function getReviewId(): int
    {
        return $this->id;
    }
}
