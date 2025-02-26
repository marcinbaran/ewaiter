<?php

namespace App\Exceptions\ApiExceptions\General;

use App\Enum\ApiError;
use App\Exceptions\ApiExceptions\ApiException;
use Illuminate\Http\Response;

class InconsistentDataException extends ApiException
{
    protected const ERROR = ApiError::INCONSISTENT_DATA;

    protected const STATUS = Response::HTTP_INTERNAL_SERVER_ERROR;
}
