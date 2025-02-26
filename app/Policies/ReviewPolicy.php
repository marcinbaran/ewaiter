<?php

namespace App\Policies;

use App\Exceptions\ApiExceptions\Bill\BillNotExistsException;
use App\Exceptions\ApiExceptions\Review\BillOwnershipException;
use App\Exceptions\ApiExceptions\Review\CannotEditCommentException;
use App\Exceptions\ApiExceptions\Review\CannotEditRestaurantCommentException;
use App\Exceptions\ApiExceptions\Review\DeliveryRatingOnlyForDeliveryAddress;
use App\Exceptions\ApiExceptions\Review\NonDeliveryShippingException;
use App\Exceptions\ApiExceptions\Review\OffensiveReviewException;
use App\Exceptions\ApiExceptions\Review\ReviewAlreadyAdded;
use App\Exceptions\ApiExceptions\Review\ReviewTimeIsUp;
use App\Models\Bill;
use App\Models\Restaurant;
use App\Models\Review;
use App\Models\UserSystem;
use App\Services\OpenAiService;
use Carbon\Carbon;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Support\Facades\DB;

class ReviewPolicy
{
    public function __construct(private readonly OpenAiService $openAIService)
    {
    }

    use HandlesAuthorization;

    public function before(UserSystem $user, string $ability, Review $review): bool|null
    {
        if ($user->id == $review->user_id || $user->isOne([UserSystem::ROLE_MANAGER, UserSystem::ROLE_ADMIN])) {
            if (in_array($ability, ['create', 'update'])) {
                return null;
            }

            return true;
        }
        return false;
    }

    public function view(UserSystem $user, Review $review): bool
    {
        return false;
    }

    public function create(UserSystem $user, Review $review): bool
    {
        $restaurant = Restaurant::findOrFail($review->restaurant_id);
        Restaurant::reconnect($restaurant);
        $bill = DB::connection('tenant')->table('bills')->where('id', $review->bill_id)->first();

        if (!$bill) {
            throw new BillNotExistsException();
        }

        $reviewExists = Review::where([
            'user_id' => $review->user_id,
            'bill_id' => $review->bill_id,
            'restaurant_id' => $review->restaurant_id
        ])->exists();

        if ($bill->delivery_type !== 'delivery_address' && $review->rating_delivery !== null) {
            throw new DeliveryRatingOnlyForDeliveryAddress();
        }

        if ($this->isReviewTimeIsUp($bill->released_at)) {
            throw new ReviewTimeIsUp();
        }

        if ($bill->user_id != $user->id) {
            throw new BillOwnershipException();
        }

        if ($reviewExists) {
            throw new ReviewAlreadyAdded();
        }

        if ($bill->delivery_type != 'delivery_address' && $review->object_type == 'delivery') {
            throw new NonDeliveryShippingException();
        }

        if ($review->comment !== null) {
            if (!$this->checkIsCorrectedMessage($review->comment)) {
                throw new OffensiveReviewException();
            }
        }

        return true;
    }

    public function isReviewTimeIsUp(?string $billReleasedAt): bool
    {
        if ($billReleasedAt == null) {
            return false;
        }

        $now = Carbon::now();
        $releasedAt = new Carbon($billReleasedAt);

        return $now->diffInHours($releasedAt) >= 48;
    }

    public function update(UserSystem $user, Review $review): bool
    {
        $restaurant = Restaurant::findOrFail($review->restaurant_id);
        Restaurant::reconnect($restaurant);
        $bill = Bill::findOrFail($review->bill_id);
        $review = Review::where([
            'user_id' => $user->id,
            'bill_id' => $review->bill_id,
            'restaurant_id' => $review->restaurant_id
        ])->first();

        if ($this->isReviewTimeIsUp($bill->released_at)) {
            throw new ReviewTimeIsUp();
        }

        if (request()->get('comment') !== null && $review->user_edited == 1) {
            throw new CannotEditCommentException();
        }

        if (request()->get('restaurant_comment') !== null && $review->restaurant_edited == 1) {
            throw new CannotEditRestaurantCommentException();
        }

        if ($review->comment !== null) {
            if (!$this->checkIsCorrectedMessage($review->comment)) {
                throw new OffensiveReviewException();
            }
        }

        return true;
    }

    public function delete(UserSystem $user, Review $review): bool
    {
        return false;
    }

    public function checkIsCorrectedMessage(string $message): bool
    {
        $thread = $this->openAIService->createThread();
        $this->openAIService->createMessage($thread->id, 'user', $message);
        $response = $this->openAIService->createRun($thread->id);

        sleep(0.3);
        $retrieveResponse = $this->openAIService->runsRetrieve($thread->id, $response->id);

        while ($retrieveResponse->status == 'in_progress' || $retrieveResponse->status == 'queued') {
            sleep(0.3);
            $retrieveResponse = $this->openAIService->runsRetrieve($thread->id, $response->id);
        }

        return filter_var($this->openAIService->getLastMessageForThread($thread->id), FILTER_VALIDATE_BOOLEAN);
    }
}
