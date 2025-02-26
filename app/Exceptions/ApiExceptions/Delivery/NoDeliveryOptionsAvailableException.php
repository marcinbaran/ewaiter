<?php

namespace App\Exceptions\ApiExceptions\Delivery;

use App\Enum\ApiError;
use App\Exceptions\ApiExceptions\ApiException;
use Illuminate\Http\Response;

class NoDeliveryOptionsAvailableException extends ApiException // FIXME: add 'Exception' suffix to class name
{
    protected const ERROR = ApiError::NO_DELIVERY_OPTIONS_AVAILABLE;

    protected const STATUS = Response::HTTP_CONFLICT;
}
