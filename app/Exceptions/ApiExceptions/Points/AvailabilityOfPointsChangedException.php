<?php

namespace App\Exceptions\ApiExceptions\Points;

use App\Enum\ApiError;
use App\Exceptions\ApiExceptions\ApiException;
use Illuminate\Http\Response;

class AvailabilityOfPointsChangedException extends ApiException
{
    protected const ERROR = ApiError::AVAILABILITY_OF_POINTS_CHANGED;

    protected const STATUS = Response::HTTP_CONFLICT;
}
