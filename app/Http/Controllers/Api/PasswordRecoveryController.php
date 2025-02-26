<?php

namespace App\Http\Controllers\Api;

use App\Exceptions\ApiExceptions\Auth\PasswordRecovery\CodeExpiredException;
use App\Exceptions\ApiExceptions\Auth\PasswordRecovery\InvalidSmsCodeException;
use App\Exceptions\ApiExceptions\Auth\UserNotFoundException;
use App\Exceptions\ApiExceptions\General\InconsistentDataException;
use App\Managers\PasswordRecoveryManager;
use App\Models\PasswordRecovery;
use App\Models\User;
use Illuminate\Http\Request;
/**
 * @OA\Tag(
 *     name="Password Recovery",
 *     description="API Endpoints for managing Password Recovery"
 * )
 */
class PasswordRecoveryController extends ApiController
{
    private $manager;

    public function __construct()
    {
        parent::__construct();
        $this->manager = new PasswordRecoveryManager();
    }
    /**
     * @OA\Post(
     *     path="/api/reset-password/request_sms_code",
     *     operationId="requestSMSCode",
     *     tags={"Password Recovery"},
     *     summary="Request SMS code for password recovery",
     *     description="Allows a user to request an SMS code for password recovery.",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="phone", type="string", description="User's phone number"),
     *             @OA\Property(property="email", type="string", description="User's email address")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="SMS code sent successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="recover_response", type="boolean", example=true)
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="User not found",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="User not found")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="Validation failed"),
     *             @OA\Property(property="errors", type="object")
     *         )
     *     )
     * )
     */
    public function requestSMSCode(Request $request)
    {
        $request->validate([
            'phone' => 'required_without:email',
            'email' => 'required_without:phone|email',
        ]);

        $params = $request->all();

        $targetUser = $this->getUserByPhoneOrEmail(phone: $params['phone'] ?? '', email: $params['email'] ?? '');

        if (! $targetUser) {
            throw new UserNotFoundException();
        }

        $result = $targetUser ? $this->manager->create($targetUser) : false;

        return \Response::json(['recover_response' => (bool) $result]);
    }
    /**
     * @OA\Put(
     *     path="/api/reset-password/set_new_password",
     *     operationId="setNewPassword",
     *     tags={"Password Recovery"},
     *     summary="Set a new password",
     *     description="Allows a user to set a new password using the SMS code.",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="phone", type="string", description="User's phone number"),
     *             @OA\Property(property="email", type="string", description="User's email address"),
     *             @OA\Property(property="smsCode", type="string", description="SMS code received by the user"),
     *             @OA\Property(property="newPassword", type="string", description="New password")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Password set successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="recovery_response", type="boolean", example=true)
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="User not found",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="User not found")
     *         )
     *     ),
     *     @OA\Response(
     *         response=410,
     *         description="Code expired",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="The code has expired")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Invalid input data",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="Invalid SMS code")
     *         )
     *     )
     * )
     */
    public function setNewPassword(Request $request)
    {
        $request->validate([
            'phone' => 'required_without:email',
            'email' => 'required_without:phone|email',
            'smsCode' => 'required',
            'newPassword' => 'required',
        ]);

        $params = $request->all();

        $user = $this->getUserByPhoneOrEmail(phone: $params['phone'] ?? '', email: $params['email'] ?? '');

        if (! $user) {
            throw new UserNotFoundException();
        }

        $params['user'] = $user;

        $passwordRecoveryData = PasswordRecovery::getLatestPasswordRecoveryData($user);

        if (! $passwordRecoveryData) {
            throw new InconsistentDataException();
        }

        if ($passwordRecoveryData->isExpired()) {
            throw new CodeExpiredException();
        }

        if ($params['smsCode'] != $passwordRecoveryData->code) {
            throw new InvalidSmsCodeException();
        }

        return \Response::json(['recovery_response' => $this->manager->update($passwordRecoveryData, $params)]);
    }

    private function getUserByPhoneOrEmail(string $phone, string $email): User|null
    {
        if ($phone) {
            $user = User::where('phone', $phone)->first();
        } elseif ($email) {
            $user = User::where('email', $email)->first();
        }

        if (! $user) {
            throw new UserNotFoundException();
        }

        return $user;
    }
}
