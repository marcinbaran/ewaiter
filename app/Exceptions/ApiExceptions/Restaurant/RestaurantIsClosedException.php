<?php

namespace App\Exceptions\ApiExceptions\Restaurant;

use App\Enum\ApiError;
use App\Exceptions\ApiExceptions\ApiException;
use Illuminate\Http\Response;

class RestaurantIsClosedException extends ApiException
{
    protected const ERROR = ApiError::RESTAURANT_IS_CLOSED;

    protected const STATUS = Response::HTTP_CONFLICT;
}
