<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
/**
 * @OA\Schema(
 *     schema="PasswordRecoveryResource",
 *     type="object",
 *     title="Password Recovery Resource",
 *     description="Resource representing the response of a password recovery request",
 *     @OA\Property(
 *         property="message",
 *         type="string",
 *         example="Password recovery email sent successfully.",
 *         description="A status message indicating the result of the password recovery request."
 *     ),
 *     @OA\Property(
 *         property="token",
 *         type="string",
 *         example="eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9...",
 *         description="The token generated for password recovery (if applicable)."
 *     ),
 *     @OA\Property(
 *         property="expires_in",
 *         type="integer",
 *         example=3600,
 *         description="The time in seconds until the token expires."
 *     )
 * )
 */
class PasswordRecoveryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return parent::toArray($request);
    }
}
