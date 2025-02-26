<?php

namespace App\Commands\Review;

use App\Commands\CommandInterface;
use App\Http\Requests\Api\ReviewRequest;
use Illuminate\Support\Facades\Auth;

final class CreateReviewCommand implements CommandInterface
{
    public function __construct(
        private int $rating_food,
        private ?int $rating_delivery,
        private ?string $comment,
        private int $user_id,
        private int $restaurant_id,
        private int $bill_id,
        private string $user_name,
    ) {
    }

    public static function createFromRequest(ReviewRequest $request): self
    {
        return new self(
            rating_food: $request->get(ReviewRequest::RATING_FOOD_KEY),
            rating_delivery: $request->get(ReviewRequest::RATING_DELIVERY_KEY),
            comment: $request->get(ReviewRequest::COMMENT_KEY),
            user_id: Auth::user()->id,
            restaurant_id: $request->get(ReviewRequest::RESTAURANT_ID_KEY),
            bill_id: $request->get(ReviewRequest::BILL_ID_KEY),
            user_name: Auth::user()->first_name ? Auth::user()->first_name : Auth::user()->email,
        );
    }

    public function toArray(): array
    {
        return [
            'rating_food' => $this->rating_food,
            'rating_delivery' => $this->rating_delivery,
            'comment' => $this->comment,
            'user_id' => $this->user_id,
            'restaurant_id' => $this->restaurant_id,
            'bill_id' => $this->bill_id,
            'user_name' => $this->user_name,
        ];
    }
}
