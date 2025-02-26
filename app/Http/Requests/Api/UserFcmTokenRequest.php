<?php

namespace App\Http\Requests\Api;

use App\Http\Requests\RequestTrait;
use Illuminate\Foundation\Http\FormRequest;
/**
 * @OA\Schema(
 *     schema="UserFcmTokenRequestPOST",
 *     type="object",
 *     @OA\Property(
 *         property="token",
 *         type="string",
 *         description="Token FCM wymagany do autoryzacji użytkownika. To pole jest wymagane i musi być typu string.",
 *         example="fcm_token_example_12345"
 *     ),
 *     required={"token"}
 * )
 *
 * @OA\Schema(
 *     schema="UserFcmTokenRequestDELETE",
 *     type="object",
 *     @OA\Property(
 *         property="token",
 *         type="integer",
 *         description="Identyfikator tokenu FCM do usunięcia. To pole jest wymagane i musi być typu integer.",
 *         example=123456
 *     ),
 *     required={"token"}
 * )
 */
class UserFcmTokenRequest extends FormRequest
{
    use RequestTrait;

    const USER_ID_KEY = 'user_id';
    const FCM_TOKEN_KEY = 'token';

    public function authorize(): bool
    {
        return true;
    }

    public function rules()
    {
        $rules = [
            self::METHOD_POST => [
                self::FCM_TOKEN_KEY => 'required|string',
            ],
            self::METHOD_DELETE => [
                self::FCM_TOKEN_KEY => 'required|integer',
            ],
        ];

        return $rules[$this->getMethod()];
    }
}
