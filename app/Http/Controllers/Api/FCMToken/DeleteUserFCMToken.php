<?php

namespace App\Http\Controllers\Api\FCMToken;

use App\Exceptions\ApiExceptions\FCMToken\TokenNotFound;
use App\Http\Controllers\Api\ApiController;
use App\Models\UserFCMToken;
/**
 * @OA\Delete(
 *     path="/api/fcm_token/{id}",
 *     operationId="deleteUserFCMToken",
 *     tags={"User FCM Token"},
 *     summary="Delete a user's FCM token",
 *     description="Deletes a Firebase Cloud Messaging (FCM) token associated with the authenticated user.",
 *     @OA\Parameter(
 *         name="token",
 *         in="path",
 *         required=true,
 *         description="FCM token to delete",
 *         @OA\Schema(
 *             type="string",
 *             example="dKJIOGJkjg123hKJHGklG123"
 *         )
 *     ),
 *     @OA\Response(
 *         response=204,
 *         description="No Content"
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Token not found"
 *     ),
 *     @OA\Response(
 *         response=401,
 *         description="Unauthorized"
 *     )
 * )
 */
class DeleteUserFCMToken extends ApiController
{
    /**
     * @throws TokenNotFound
     */
    public function __invoke($token)
    {
        $userFCMToken = UserFCMToken::where('token', $token)->first();

        if (!$userFCMToken) {
            throw new TokenNotFound();
        }

        $userFCMToken->delete();

        return response()->noContent();
    }
}
