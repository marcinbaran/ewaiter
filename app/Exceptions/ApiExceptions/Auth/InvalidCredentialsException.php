<?php

namespace App\Exceptions\ApiExceptions\Auth;

use App\Enum\ApiError;
use App\Exceptions\ApiExceptions\ApiException;
use Illuminate\Http\Response;

class InvalidCredentialsException extends ApiException implements AuthExceptionInterface
{
    protected const ERROR = ApiError::INVALID_CREDENTIALS;

    protected const STATUS = Response::HTTP_BAD_REQUEST;
}
