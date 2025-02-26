<?php

namespace App\Handlers\Command\Review;

use App\Commands\Review\UpdateReviewCommand;
use App\Handlers\Command\CommandHandlerInterface;
use App\Mail\ReviewMail;
use App\Models\Restaurant;
use App\Models\Review;
use Carbon\Carbon;
use Ecotone\Modelling\Attribute\CommandHandler;
use Ecotone\Modelling\CommandBus;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Mail;

class UpdateReviewHandler implements CommandHandlerInterface
{
    public function __construct(
        protected CommandBus $commandBus,
    )
    {
    }

    #[CommandHandler]
    public function updateReview(UpdateReviewCommand $command): void
    {
        $review = Review::findOrFail($command->getReviewId());

        if (Gate::allows('update', $review)) {
            $newReview = $command->toArray();
            if (request('comment') !== null) {
                $newReview['comment'] = request('comment');
                $newReview['user_edited'] = 1;
                $this->sendEmailToRestaurant($review->restaurant_id, $review['bill_id']);
            }

            if (request('restaurant_comment') !== null) {
                $newReview['restaurant_comment'] = request('restaurant_comment');
                $newReview['restaurant_edited'] = 1;
                if ($review->restaurant_comment_created_at === null) {
                    $newReview['restaurant_comment_created_at'] = Carbon::now();
                }
            }

            $review->update($newReview);
        } else {
            abort(403);
        }
    }

    public function sendEmailToRestaurant(int $restaurantId, int $billId): void
    {
        $restaurantEmail = Restaurant::where('id', $restaurantId)->first()->manager_email;

        $dataToSend = [
            'To' => $restaurantEmail,
            'title' => __('emails.review_edited_title'),
            'subject' => __('emails.review_edited_subject'),
            'name' => __('emails.name'),
            'message' => __('emails.edited_review', ['orderNumber' => $billId]),
        ];

        if ($restaurantEmail) {
            Mail::to($restaurantEmail)->send(new ReviewMail($dataToSend));
        }
    }
}
