<?php

namespace App\Http\Controllers\Actions\Api\Review;

use App\Http\Controllers\Actions\Api\ApiQueryActionBase;
use App\Http\Requests\Api\ReviewRequest;
use App\Http\Resources\Api\ReviewResource;
use App\Models\User;
use App\Queries\Review\GetReviewsQuery;
/**
 * @OA\Get(
 *     path="/api/review",
 *     operationId="getReviews",
 *     tags={"Reviews"},
 *     summary="Get a list of reviews",
 *     description="Retrieve a list of reviews, with an optional filter to exclude reviews from deleted users.",
 *     @OA\Parameter(
 *         name="filter",
 *         in="query",
 *         required=false,
 *         @OA\Schema(type="string"),
 *         description="Filter reviews by specific criteria."
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="A list of reviews",
 *         @OA\JsonContent(
 *             type="array",
 *             @OA\Items(ref="#/components/schemas/Review")
 *         )
 *     ),
 *     @OA\Response(
 *         response=401,
 *         description="Unauthorized"
 *     )
 * )
 */

class GetReviewsQueryAction extends ApiQueryActionBase
{
    public function __construct(ReviewRequest $request)
    {
        parent::__construct($request);
    }

    public function handle()
    {
        $responseData = GetReviewsQuery::createFromRequest($this->request);
        $responseData = $responseData->execute();
        $filteredResponseData = $responseData->reject(function ($review) {
            $user = User::find($review->user_id);
            return $user && $user->first_name == "deleted_user";
        });
        $this->sendResourceResponse($filteredResponseData, ReviewResource::class);
    }
}
