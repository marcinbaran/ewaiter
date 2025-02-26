<?php

namespace App\Exceptions\ApiExceptions\Auth;

use App\Enum\ApiError;
use App\Exceptions\ApiExceptions\ApiException;
use Illuminate\Http\Response;

class EmailAlreadyExistsException extends ApiException implements AuthExceptionInterface
{
    protected const ERROR = ApiError::EMAIL_ALREADY_EXISTS;

    protected const STATUS = Response::HTTP_BAD_REQUEST;
}
