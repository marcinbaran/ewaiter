<?php

namespace App\Exceptions\ApiExceptions\Auth;

use App\Enum\ApiError;
use App\Exceptions\ApiExceptions\ApiException;
use Illuminate\Http\Response;

class ActiveUserNotFoundException extends ApiException implements AuthExceptionInterface
{
    protected const ERROR = ApiError::ACTIVE_USER_NOT_FOUND;

    protected const STATUS = Response::HTTP_UNAUTHORIZED;
}
