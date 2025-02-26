<?php

namespace App\Http\Controllers\Api\FCMToken;

use App\Http\Controllers\Api\ApiController;
use App\Http\Requests\Api\UserFcmTokenRequest;
use App\Models\UserFCMToken;
use Illuminate\Support\Facades\Auth;
/**
 * @OA\Post(
 *     path="/api/fcm_token",
 *     operationId="createUserFCMToken",
 *     tags={"User FCM Token"},
 *     summary="Create a new FCM token for a user",
 *     description="Saves a new Firebase Cloud Messaging (FCM) token for the authenticated user.",
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(
 *                 property="token",
 *                 type="string",
 *                 description="FCM token",
 *                 example="dKJIOGJkjg123hKJHGklG123"
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=204,
 *         description="No Content"
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="Bad Request"
 *     ),
 *     @OA\Response(
 *         response=401,
 *         description="Unauthorized"
 *     )
 * )
 */
class CreateUserFCMToken extends ApiController
{
    public function __invoke(UserFcmTokenRequest $request)
    {
        UserFCMToken::firstOrCreate([
            'user_id' => Auth::user()->id,
            'token' => $request->get('token'),
        ]);

        return response()->noContent();
    }
}
