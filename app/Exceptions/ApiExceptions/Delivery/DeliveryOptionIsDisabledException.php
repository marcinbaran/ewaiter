<?php

namespace App\Exceptions\ApiExceptions\Delivery;

use App\Enum\ApiError;
use App\Exceptions\ApiExceptions\ApiException;
use Illuminate\Http\Response;

class DeliveryOptionIsDisabledException extends ApiException
{
    protected const ERROR = ApiError::DELIVERY_OPTION_IS_DISABLED;

    protected const STATUS = Response::HTTP_CONFLICT;
}
