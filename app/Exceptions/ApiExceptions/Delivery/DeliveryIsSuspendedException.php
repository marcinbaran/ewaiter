<?php

namespace App\Exceptions\ApiExceptions\Delivery;

use App\Enum\ApiError;
use App\Exceptions\ApiExceptions\ApiException;
use Illuminate\Http\Response;

class DeliveryIsSuspendedException extends ApiException
{
    protected const ERROR = ApiError::ADDRESS_DELIVERY_SUSPENDED;
    protected const STATUS = Response::HTTP_CONFLICT;

}
