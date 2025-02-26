<?php

namespace App\Exceptions\ApiExceptions\Auth;

use App\Enum\ApiError;
use App\Exceptions\ApiExceptions\ApiException;
use Illuminate\Http\Response;

class ExternalLoginInvalidTokenException extends ApiException implements AuthExceptionInterface
{
    protected const ERROR = ApiError::INVALID_TOKEN;

    protected const STATUS = Response::HTTP_UNAUTHORIZED;
}
