<?php

namespace App\Exceptions\ApiExceptions\Points;

use App\Enum\ApiError;
use App\Exceptions\ApiExceptions\ApiException;
use Illuminate\Http\Response;

class PointsCannotBeSendException extends ApiException
{
    protected const ERROR = ApiError::POINTS_CANNOT_BE_SEND;

    protected const STATUS = Response::HTTP_BAD_REQUEST;
}
