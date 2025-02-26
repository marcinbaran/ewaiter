<?php

namespace App\Exceptions\ApiExceptions\Auth\PasswordRecovery;

use App\Enum\ApiError;
use App\Exceptions\ApiExceptions\ApiException;
use App\Exceptions\ApiExceptions\Auth\AuthExceptionInterface;
use Illuminate\Http\Response;

class CodeExpiredException extends ApiException implements AuthExceptionInterface
{
    protected const ERROR = ApiError::CODE_EXPIRED;

    protected const STATUS = Response::HTTP_GONE;
}
