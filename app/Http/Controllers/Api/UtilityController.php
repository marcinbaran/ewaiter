<?php

namespace App\Http\Controllers\Api;

use App\Enum\ApiError;
/**
 * @OA\Tag(
 *     name="Utilities",
 *     description="API Endpoints for Utilities."
 * )
 */
class UtilityController extends ApiController
{
    /**
     * @OA\Get(
     *     path="/api/errors",
     *     operationId="getApiErrorList",
     *     tags={"Utilities"},
     *     summary="Retrieve a list of API error codes and their descriptions",
     *     description="Returns a list of all possible API error codes and their corresponding descriptions.",
     *     @OA\Response(
     *         response=200,
     *         description="Successfully retrieved the list of API errors",
     *         @OA\JsonContent(
     *             type="object",
     *             additionalProperties={
     *                 @OA\Property(type="string")
     *             },
     *             example={
     *                 "INVALID_REQUEST": "The request was invalid or cannot be otherwise served.",
     *                 "AUTHENTICATION_FAILED": "Authentication credentials are missing or invalid.",
     *                 "FORBIDDEN": "You do not have permission to access this resource.",
     *                 "NOT_FOUND": "The requested resource could not be found.",
     *                 "SERVER_ERROR": "An unexpected error occurred on the server."
     *             }
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal server error",
     *         @OA\JsonContent(
     *             type="object",
     *             properties={
     *                 @OA\Property(property="error", type="string", example="An unexpected error occurred.")
     *             }
     *         )
     *     ),
     *     security={{"bearerAuth":{}}}
     * )
     */
    public function getApiErrorList()
    {
        return ApiError::getKeyValuePairs();
    }
}
