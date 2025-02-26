<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Managers;

use App\Exceptions\ApiExceptions\Auth\PasswordRecovery\TooManyAttemptsForPasswordRecoveryException;
use App\Models\PasswordRecovery;
use App\Models\User;
use App\Models\UserSystem;
use App\Services\SerwerSMSService;

class PasswordRecoveryManager
{
    /**
     * @param User $user
     *
     * @return bool
     */
    public function create(User|UserSystem $user)
    {
        $currentRequest = PasswordRecovery::getLatestPasswordRecoveryData($user);

        if ($currentRequest && ! $currentRequest->isExpired()) {
            if ($currentRequest->isRecoveryCodeInvalid()) {
                throw new TooManyAttemptsForPasswordRecoveryException();
            }

            $currentRequest->attempts++;
            $currentRequest->update();
        } else {
            $currentRequest = PasswordRecovery::create([
                'user_id' => $user->id,
                'code' => mt_rand(100000, 999999),
                'attempts' => 1,
                'used' => 0,
            ]);
        }

        return (bool) (new SerwerSMSService())->sendResetPasswordCodeSMS($user->phone, $currentRequest->code);
    }

    /**
     * @param PasswordRecovery $pr
     * @param array $params
     *
     * @return bool
     */
    public function update(PasswordRecovery $pr, array $params)
    {
        if ($pr->code == $params['smsCode']) {
            $params['user']->password = bcrypt($params['newPassword']);
            $params['user']->save();

            $pr->used = true;
            $pr->update();

            return true;
        }

        return false;
    }
}
