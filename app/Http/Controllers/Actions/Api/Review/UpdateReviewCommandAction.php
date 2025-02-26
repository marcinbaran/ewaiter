<?php

namespace App\Http\Controllers\Actions\Api\Review;

use App\Commands\Review\UpdateReviewCommand;
use App\Http\Controllers\Actions\Api\ApiCommandActionBase;
use App\Http\Requests\Api\ReviewRequest;
/**
 * @OA\Put(
 *     path="/api/review/{id}",
 *     operationId="updateReview",
 *     tags={"Reviews"},
 *     summary="Update a review",
 *     description="Update an existing review by its ID.",
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         @OA\Schema(type="integer"),
 *         description="ID of the review to update"
 *     ),
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="rating", type="integer", example=5, description="Rating of the review"),
 *             @OA\Property(property="comment", type="string", example="Great service!", description="Comment of the review")
 *         )
 *     ),
 *     @OA\Response(
 *         response=204,
 *         description="Review successfully updated"
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Review not found"
 *     ),
 *     @OA\Response(
 *         response=401,
 *         description="Unauthorized"
 *     ),
 *     @OA\Response(
 *         response=422,
 *         description="Validation error"
 *     )
 * )
 */
class UpdateReviewCommandAction extends ApiCommandActionBase
{
    public function __construct(ReviewRequest $request)
    {
        parent::__construct($request);
    }

    public function handle()
    {
        $this->commandBus->send(UpdateReviewCommand::createFromRequest($this->request));

        response()->noContent()->send();
    }
}
