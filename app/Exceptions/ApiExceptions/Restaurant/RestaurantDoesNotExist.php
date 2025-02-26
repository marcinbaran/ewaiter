<?php

namespace App\Exceptions\ApiExceptions\Restaurant;

use App\Enum\ApiError;
use App\Exceptions\ApiExceptions\ApiException;
use Illuminate\Http\Response;

class RestaurantDoesNotExist extends ApiException
{
    protected const ERROR = ApiError::RESTAURANT_DOES_NOT_EXIST;
    protected const STATUS = Response::HTTP_NOT_FOUND;

}
