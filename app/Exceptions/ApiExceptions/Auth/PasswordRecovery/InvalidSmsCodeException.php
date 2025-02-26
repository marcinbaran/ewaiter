<?php

namespace App\Exceptions\ApiExceptions\Auth\PasswordRecovery;

use App\Enum\ApiError;
use App\Exceptions\ApiExceptions\ApiException;
use App\Exceptions\ApiExceptions\Auth\AuthExceptionInterface;
use Illuminate\Http\Response;

class InvalidSmsCodeException extends ApiException implements AuthExceptionInterface
{
    protected const ERROR = ApiError::INVALID_SMS_CODE;

    protected const STATUS = Response::HTTP_UNPROCESSABLE_ENTITY;
}
