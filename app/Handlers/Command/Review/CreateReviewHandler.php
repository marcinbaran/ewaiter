<?php

namespace App\Handlers\Command\Review;

use App\Commands\Review\CreateReviewCommand;
use App\Mail\ReviewMail;
use App\Models\Restaurant;
use App\Models\Review;
use App\Repositories\MultiTentantRepositoryTrait;
use Ecotone\Modelling\Attribute\CommandHandler;
use Ecotone\Modelling\CommandBus;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Mail;
use Symfony\Component\HttpFoundation\Response;

class CreateReviewHandler
{
    use MultiTentantRepositoryTrait;

    public function __construct(
        protected CommandBus $commandBus,
    )
    {
    }

    #[CommandHandler]
    public function createReview(CreateReviewCommand $command): void
    {
        $reviewData = $command->toArray();

        if (Gate::allows('create', new Review($reviewData))) {
            $restaurantId = $reviewData['restaurant_id'];
            $review = Review::create($reviewData);
            $this->sendEmailToRestaurant($restaurantId, $review['bill_id']);
        } else {
            abort(Response::HTTP_FORBIDDEN);
        }
    }

    public function sendEmailToRestaurant(int $restaurantId, int $billId): void
    {
        $restaurantEmail = Restaurant::where('id', $restaurantId)->first()->manager_email;

        $dataToSend = [
            'To' => $restaurantEmail,
            'title' => __('emails.review_title'),
            'subject' => __('emails.review_subject'),
            'name' => __('emails.name'),
            'message' => __('emails.new_review', ['orderNumber' => $billId]),
        ];

        if ($restaurantEmail) {
            Mail::to($restaurantEmail)->send(new ReviewMail($dataToSend));
        }
    }
}
