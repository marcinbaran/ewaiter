<?php

namespace App\Http\Controllers\Actions\Api\Review;

use App\Commands\Review\CreateReviewCommand;
use App\Http\Controllers\Actions\Api\ApiCommandActionBase;
use App\Http\Requests\Api\ReviewRequest;
use Symfony\Component\HttpFoundation\Response;
/**
 * @OA\Post(
 *     path="/api/review",
 *     operationId="createReview",
 *     tags={"Reviews"},
 *     summary="Create a new review",
 *     description="Creates a new review based on the provided data.",
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(ref="#/components/schemas/ReviewRequest_POST")
 *     ),
 *     @OA\Response(
 *         response=204,
 *         description="Review created successfully"
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="Bad request"
 *     ),
 *     @OA\Response(
 *         response=401,
 *         description="Unauthorized"
 *     )
 * )
 */
class CreateReviewCommandAction extends ApiCommandActionBase
{
    public function __construct(ReviewRequest $request)
    {
        parent::__construct($request);
    }

    public function handle()
    {
        $this->commandBus->send(CreateReviewCommand::createFromRequest($this->request));

        response(null, Response::HTTP_NO_CONTENT)->send();
    }
}
