<?php

namespace App\Commands\Review;

use App\Commands\CommandInterface;
use App\Http\Requests\Api\ReviewRequest;

class DeleteReviewCommand implements CommandInterface
{
    public function __construct(
        private int $id,
    ) {
    }

    public static function createFromRequest(ReviewRequest $request): self
    {
        return new self(
            id: $request->get(ReviewRequest::ID_KEY),
        );
    }

    public function getReviewId(): int
    {
        return $this->id;
    }
}
