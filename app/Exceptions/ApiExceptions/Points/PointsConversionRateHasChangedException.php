<?php

namespace App\Exceptions\ApiExceptions\Points;

use App\Enum\ApiError;
use App\Exceptions\ApiExceptions\ApiException;
use Illuminate\Http\Response;

class PointsConversionRateHasChangedException extends ApiException
{
    protected const ERROR = ApiError::POINTS_CONVERSION_RATE_HAS_CHANGED;
    protected const STATUS = Response::HTTP_CONFLICT;

}
