<?php

namespace App\Exceptions\ApiExceptions\General;

use App\Enum\ApiError;
use App\Exceptions\ApiExceptions\ApiException;
use Illuminate\Http\Response;

class InvalidRequestDataException extends ApiException
{
    protected const ERROR = ApiError::INVALID_DATA;

    protected const STATUS = Response::HTTP_UNPROCESSABLE_ENTITY;
}
