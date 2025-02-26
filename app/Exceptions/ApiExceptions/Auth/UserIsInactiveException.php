<?php

namespace App\Exceptions\ApiExceptions\Auth;

use App\Enum\ApiError;
use App\Exceptions\ApiExceptions\ApiException;
use Illuminate\Http\Response;

class UserIsInactiveException extends ApiException implements AuthExceptionInterface
{
    protected const ERROR = ApiError::USER_NOT_ACTIVE;

    protected const STATUS = Response::HTTP_FORBIDDEN;
}
