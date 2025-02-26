<?php

namespace App\Exceptions\ApiExceptions\Review;

use App\Enum\ApiError;
use App\Exceptions\ApiExceptions\ApiException;
use Illuminate\Http\Response;

class NonDeliveryShippingException extends ApiException
{
    protected const ERROR = ApiError::NON_DELIVERY_SHIPPING;

    protected const STATUS = Response::HTTP_BAD_REQUEST;
}
