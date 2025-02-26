<?php

namespace App\Exceptions\ApiExceptions\Auth\PasswordRecovery;

use App\Enum\ApiError;
use App\Exceptions\ApiExceptions\ApiException;
use App\Exceptions\ApiExceptions\Auth\AuthExceptionInterface;
use Illuminate\Http\Response;

class TooManyAttemptsForPasswordRecoveryException extends ApiException implements AuthExceptionInterface
{
    protected const ERROR = ApiError::TOO_MANY_ATTEMPTS_FOR_PASSWORD_RECOVERY;

    protected const STATUS = Response::HTTP_TOO_MANY_REQUESTS;
}
