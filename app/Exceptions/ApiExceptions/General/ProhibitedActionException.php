<?php

namespace App\Exceptions\ApiExceptions\General;

use App\Enum\ApiError;
use App\Exceptions\ApiExceptions\ApiException;
use Illuminate\Http\Response;

class ProhibitedActionException extends ApiException
{
    protected const ERROR = ApiError::ACTION_PROHIBITED;

    protected const STATUS = Response::HTTP_FORBIDDEN;
}
