<?php

namespace App\Exceptions\ApiExceptions\Polygon;

use App\Enum\ApiError;
use App\Exceptions\ApiExceptions\ApiException;
use Illuminate\Http\Response;

class RangePolygonNotSet extends ApiException
{
    protected const ERROR = ApiError::RANGE_POLYGON_NOT_SET;

    protected const STATUS = Response::HTTP_BAD_REQUEST;
}
