<?php

namespace App\Exceptions\ApiExceptions;

use App\Enum\ApiError;
use Illuminate\Http\Response;

class ValidationException extends ApiException
{
    protected const ERROR = ApiError::VALIDATION_FAILED;

    protected const STATUS = Response::HTTP_BAD_REQUEST;
}
