<?php

namespace App\Exceptions\ApiExceptions\Order;

use App\Enum\ApiError;
use App\Exceptions\ApiExceptions\ApiException;
use Illuminate\Http\Response;

class MinimumOrderValueNotExceededException extends ApiException
{
    protected const ERROR = ApiError::MINIMUM_ORDER_VALUE_NOT_EXCEEDED;
    protected const STATUS = Response::HTTP_CONFLICT;
}
