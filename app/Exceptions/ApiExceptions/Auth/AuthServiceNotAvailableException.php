<?php

namespace App\Exceptions\ApiExceptions\Auth;

use App\Enum\ApiError;
use App\Exceptions\ApiExceptions\ApiException;
use Illuminate\Http\Response;

class AuthServiceNotAvailableException extends ApiException implements AuthExceptionInterface
{
    protected const ERROR = ApiError::AUTH_SERVICE_HAS_GONE_AWAY;

    protected const STATUS = Response::HTTP_INTERNAL_SERVER_ERROR;
}
