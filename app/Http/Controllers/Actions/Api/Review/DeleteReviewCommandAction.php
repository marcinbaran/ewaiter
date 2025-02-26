<?php

namespace App\Http\Controllers\Actions\Api\Review;

use App\Commands\Review\DeleteReviewCommand;
use App\Http\Controllers\Actions\Api\ApiCommandActionBase;
use App\Http\Requests\Api\ReviewRequest;
use Symfony\Component\HttpFoundation\Response;
/**
 * @OA\Delete(
 *     path="/api/review/{id}",
 *     operationId="deleteReview",
 *     tags={"Reviews"},
 *     summary="Delete a review",
 *     description="Delete a review by its ID.",
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         @OA\Schema(type="integer"),
 *         description="ID of the review to delete"
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Review successfully deleted"
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Review not found"
 *     ),
 *     @OA\Response(
 *         response=401,
 *         description="Unauthorized"
 *     )
 * )
 */
class DeleteReviewCommandAction extends ApiCommandActionBase
{
    public function __construct(ReviewRequest $request)
    {
        parent::__construct($request);
    }

    public function handle()
    {
        $this->commandBus->send(DeleteReviewCommand::createFromRequest($this->request));

        response(null, Response::HTTP_OK)->send();
    }
}
