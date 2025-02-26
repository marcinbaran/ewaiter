<?php

namespace App\Exceptions\ApiExceptions\TPay;

use App\Enum\ApiError;
use App\Exceptions\ApiExceptions\ApiException;
use Illuminate\Http\Response;

class InvalidCredentialException extends ApiException
{
    protected const ERROR = ApiError::INVALID_CREDENTIAL;
    protected const STATUS = Response::HTTP_UNAUTHORIZED;
}
