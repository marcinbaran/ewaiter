<?php

namespace App\Exceptions\ApiExceptions\Delivery;

use App\Enum\ApiError;
use App\Exceptions\ApiExceptions\ApiException;
use Illuminate\Http\Response;

class NoValidDeliveryTypeException extends ApiException
{
    protected const ERROR = ApiError::NO_VALID_DELIVERY_TYPE;

    protected const STATUS = Response::HTTP_CONFLICT;
}
