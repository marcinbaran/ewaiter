<?php

namespace App\Http\Controllers\Actions\Api\Restaurant;

use App\Commands\Restaurant\SaveVisitCommand;
use App\DTO\Visits\VisitDto;
use App\Http\Controllers\Actions\Api\ApiCommandActionBase;
use App\Http\Requests\Api\SaveVisitRequest;
use Symfony\Component\HttpFoundation\Response;
/**
 * @OA\Post(
 *     path="/api/save-visit",
 *     summary="[TENANT] Save a visit to the restaurant",
 *     tags={"[TENANT] Restaurant"},
 *     description="Records a visit to the restaurant, including visit details such as user information and visit time.",
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\MediaType(
 *             mediaType="application/json",
 *             @OA\Schema(
 *                 type="object",
 *                 required={"user_id", "visit_time"},
 *                 @OA\Property(
 *                     property="user_id",
 *                     type="integer",
 *                     description="The ID of the user making the visit",
 *                     example=1
 *                 ),
 *                 @OA\Property(
 *                     property="visit_time",
 *                     type="string",
 *                     format="date-time",
 *                     description="The time of the visit",
 *                     example="2024-07-30T12:34:56Z"
 *                 ),
 *                 @OA\Property(
 *                     property="comments",
 *                     type="string",
 *                     description="Any additional comments about the visit",
 *                     example="Great service!"
 *                 )
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=204,
 *         description="Visit successfully saved",
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="Invalid request data",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="error", type="string", example="Invalid visit data provided.")
 *         )
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Internal server error",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="error", type="string", example="An unexpected error occurred.")
 *         )
 *     ),
 *     security={{"bearerAuth":{}}}
 * )
 */
class SaveVisitAction extends ApiCommandActionBase
{
    public function __construct(SaveVisitRequest $request)
    {
        parent::__construct($request);
    }

    public function handle()
    {
        $this->commandBus->send(new SaveVisitCommand(VisitDto::createFromRequest($this->request)));

        response(null, Response::HTTP_NO_CONTENT)->send();
    }
}
