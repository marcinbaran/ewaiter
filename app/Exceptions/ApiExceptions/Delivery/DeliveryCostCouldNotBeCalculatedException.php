<?php

namespace App\Exceptions\ApiExceptions\Delivery;

use App\Enum\ApiError;
use App\Exceptions\ApiExceptions\ApiException;
use Illuminate\Http\Response;

class DeliveryCostCouldNotBeCalculatedException extends ApiException
{
    protected const ERROR = ApiError::DELIVERY_COST_COULD_NOT_BE_CALCULATED;
    protected const STATUS = Response::HTTP_INTERNAL_SERVER_ERROR;

}
