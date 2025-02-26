<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ReviewRequest;
use App\Http\Resources\Admin\BillResource;
use App\Models\Bill;
use App\Models\Review;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ReviewController extends Controller
{

    public function index(Request $request)
    {
        session()->put($this->redirectUrlSessionKey, $request->fullUrl());

        return view('admin.reviews.index')->with([]);
    }

    public function show(ReviewRequest $request, Review $review)
    {
        $reviewWithExpirationTimes = $this->addExpirationTimesToReview($review);
        $bill = Bill::findOrFail($review->bill_id);

        return view('admin.reviews.show')->with([
            'review' => $reviewWithExpirationTimes,
            'data' => new BillResource($bill),
        ]);
    }

    private function addExpirationTimesToReview(Review $review)
    {
        $currentTime = Carbon::now();
        $timeToExpireRestaurantComment = $review->created_at->addHours(48);
        $review->updated_at = $review->updated_at->addHours(48);

        $timeToExpireRestaurantEdited = $review->user_edited ? $review->updated_at->copy() : $review->updated_at;
        $timeToExpireUserEdited = $review->restaurant_edited ? $review->updated_at->copy()->addHours(48) : $review->updated_at;


        $isRestaurantCommentExpired = $currentTime->greaterThan($timeToExpireRestaurantComment);
        $isRestaurantEditExpired = $timeToExpireRestaurantEdited ? $currentTime->greaterThan($timeToExpireRestaurantEdited) : false;

        $isUserEditExpired = $timeToExpireUserEdited ? $currentTime->greaterThan($timeToExpireUserEdited) : false;

        $review->timesToExpire = [
            'timeToExpireRestaurantComment' => $timeToExpireRestaurantComment,
            'timeToExpireRestaurantEdited' => $timeToExpireRestaurantEdited,
            'isRestaurantCommentExpired' => $isRestaurantCommentExpired,
            'isRestaurantEditExpired' => $isRestaurantEditExpired,
            'timeToExpireUserEdited' => $timeToExpireUserEdited,
            'isUserEditExpired' => $isUserEditExpired,
        ];
        return $review;
    }

    public function update(ReviewRequest $request, Review $review)
    {
        try {
            $review->restaurant_comment = $request->restaurant_comment;
            $review->restaurant_comment_created_at = Carbon::now();
            if ($review->user_edited) {
                $review->restaurant_edited = 1;
                $request->session()->flash('alert-success', __('admin.reviewsTable.alert.update'));
            } else {
                $request->session()->flash('alert-success', __('admin.reviewsTable.alert.store'));
            }

            $review->save();

        } catch (Exception $e) {
            $request->session()->flash('alert-danger', __('admin.reviewsTable.alert.error'));
            Log::error($e->getMessage());
            return redirect()->route('admin.reviews.show', ['review' => $review]);
        }

        return redirect()->route('admin.reviews.show', ['review' => $review]);
    }
}
