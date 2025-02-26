<?php

namespace App\Exceptions\ApiExceptions\Restaurant;

use App\Enum\ApiError;
use App\Exceptions\ApiExceptions\ApiException;
use Illuminate\Http\Response;

class RestaurantConfigurationHaveChangedException extends ApiException
{
    protected const ERROR = ApiError::RESTAURANT_CONFIGURATION_HAVE_CHANGED;
    protected const STATUS = Response::HTTP_CONFLICT;

}
