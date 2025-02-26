<?php

namespace App\Exceptions\ApiExceptions\Order;

use App\Enum\ApiError;
use App\Exceptions\ApiExceptions\ApiException;
use Illuminate\Http\Response;

class AvailabilityOfProductsChangedException extends ApiException
{
    protected const ERROR = ApiError::AVAILABILITY_OF_PRODUCTS_CHANGED;

    protected const STATUS = Response::HTTP_CONFLICT;
}
